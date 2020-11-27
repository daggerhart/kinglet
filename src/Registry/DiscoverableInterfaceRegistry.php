<?php

namespace Kinglet\Registry;

use Kinglet\FileSystem\Finder;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use SplFileInfo;

/**
 * Class DiscoverableInterfaceRegistry
 *
 * @package Kinglet
 */
class DiscoverableInterfaceRegistry extends ClassRegistry implements ClassRegistryInterface {

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
	 * {@inheritdoc}
	 */
	public function all() {
		if ( empty( $this->items ) ) {
			$this->items = $this->discover();
		}

		return $this->items;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIterator() {
		if ( empty( $this->items ) ) {
			$this->items = $this->discover();
		}

		return parent::getIterator();
	}

	/**
	 * {@inheritdoc}
	 */
	public function has( $key ) {
		if ( empty( $this->items ) ) {
			$this->items = $this->discover();
		}

		return isset( $this->items[ $key ] );
	}

	/**
	 * @param string $key
	 *
	 * @return string
	 * @throws RuntimeException
	 */
	public function get( $key ) {
		if ( ! $this->has( $key ) ) {
			throw new RuntimeException( 'Definition ID not found: ' . $key );
		}

		return $this->items[ $key ];
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
	 * Go searching for the classes that implement the given interface.
	 *
	 * @return array
	 */
	protected function discover() {
		$definitions = [];

		$i = 0;
		foreach ( $this->getSources() as $namespace => $location ) {
			$finder = new Finder();
			$results = $finder->recurse()->in( $location )->files( '*.php' );
			$replacements = [
				$location => '',
				'.php' => '',
				DIRECTORY_SEPARATOR => '\\',
			];

			/** @var SplFileInfo $file */
			foreach ( $results as $file ) {
				$class = strtr( $file->getRealPath(), $replacements );
				$class = trim( $class, '\\' );

				if ( is_string( $namespace ) && ! is_numeric( $namespace ) ) {
					// Convert path into namespace using PSR-4 standard.
					$namespace = rtrim( $namespace, '\\' ) . '\\';
					$class = $namespace . $class;
				}

				try {
					$reflection = new ReflectionClass( $class );
					if ( ! $reflection->isAbstract() && ! $reflection->isInterface() && in_array( $this->interfaceName, $reflection->getInterfaceNames() ) ) {
						$key = $i;
						if ( $this->idMethodName && is_callable( [ $class, $this->idMethodName ] ) ) {
							$instance = new $class;
							$key = call_user_func( [ $instance, $this->idMethodName ] );
						}
						$definitions[ $key ] = $class;
					}
				}
				catch ( ReflectionException $exception ) {
					// Fail silently ... for now.
				}

				$i ++;
			}
		}

		return $definitions;
	}

}
