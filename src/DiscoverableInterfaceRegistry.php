<?php

namespace Kinglet;

use Kinglet\FileSystem\Finder;

/**
 * Class DiscoverableInterfaceRegistry
 *
 * @package Kinglet
 */
class DiscoverableInterfaceRegistry extends Registry {

	/**
	 * Interface to register.
	 * eg, "\Kinglet\Template\TemplateCallableInterface"
	 *
	 * @var string
	 */
	protected $interfaceName;

	/**
	 * Name of method on the given interface that uniquely identifies the implementation.
	 *
	 * @var null
	 */
	protected $idMethodName;

	/**
	 * Name of WordPress filter used to dynamically collect sources.
	 *
	 * @var string
	 */
	protected $sourcesFilterName;

	/**
	 * Array of filesystem paths to discover. If the files in the given path are
	 * namespaced, provide the namespace as the key for the source value.
	 *
	 * @var array
	 */
	protected $sources = [];

	/**
	 * Discovered class definitions.
	 *
	 * @var array
	 */
	protected $items = [];

	/**
	 * Psr4Registry constructor.
	 *
	 * @param string $interface_name
	 * @param string $sources_filter_name
	 * @param string|null $id_method_name
	 */
	public function __construct( $interface_name, $id_method_name = null, $sources_filter_name = null ) {
		$this->interfaceName = $interface_name;
		$this->idMethodName = $id_method_name;
		$this->sourcesFilterName = $sources_filter_name;
		parent::__construct();
	}

	/**
	 * @return array
	 * @throws \ReflectionException
	 */
	public function all() {
		if ( empty( $this->items ) ) {
			$this->items = $this->discover();
		}

		return $this->items;
	}

	/**
	 * @param string $id
	 * @return string
	 * @throws \RuntimeException
	 */
	public function get( $id ) {
		if ( empty( $this->items[ $id ] ) ) {
			throw new \RuntimeException( 'Definition ID not found: ' . $id );
		}

		return $this->items[ $id ];
	}

	/**
	 * Set a list of sources to where interface implementations can be discovered.
	 *
	 * @param array $sources
	 */
	public function setSources( $sources ) {
		$this->sources = $sources;
	}

	/**
	 * Get a list of all sources where interfaces can be discovered.
	 *
	 * @return array
	 */
	public function getSources() {
		if ( $this->sourcesFilterName ) {
			$this->setSources( apply_filters( $this->sourcesFilterName, [] ) );
		}

		return $this->sources;
	}

	/**
	 * @return array
	 * @throws \ReflectionException
	 */
	protected function discover() {
		$definitions = [];

		$i = 0;
		foreach ( $this->getSources() as $namespace => $location ) {
			$finder = new Finder();
			$results = $finder->in( $location )->files( '*.php' );

			/** @var \SplFileInfo $file */
			foreach ( $results as $file ) {
				$class = str_replace( '.php', '', $file->getFilename() );

				if ( is_string( $namespace ) && !is_numeric( $namespace ) ) {
					// Convert path into namespace using PSR-4 standard.
					$namespace = rtrim( $namespace, '\\' ) . '\\';
					$class = $namespace . str_replace( ['.php', DIRECTORY_SEPARATOR], ['', '\\'], $file->getBasename() );
				}

				$reflection = new \ReflectionClass( $class );

				if ( !$reflection->isAbstract() && !$reflection->isInterface() && in_array( $this->interfaceName, $reflection->getInterfaceNames() ) ) {
					$id = $i;
					if ( $this->idMethodName && is_callable( [ $class, $this->idMethodName ] ) ) {
						$id = call_user_func( [ $class, $this->idMethodName ] );
					}
					$definitions[ $id ] = $class;
				}

				$i++;
			}
		}

		return $definitions;
	}

}
