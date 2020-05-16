<?php
/**
 * Heavily inspired by symfony/finder
 * @link https://github.com/symfony/finder
 */
namespace Kinglet\FileSystem;

use Kinglet\Container\ContainerInterface;
use Kinglet\Container\ContainerInjectionInterface;

/**
 * Class Finder.
 *
 * @package Kinglet\FileSystem
 */
class Finder implements ContainerInjectionInterface {

	/**
	 * @var bool
	 */
	protected $recursive = FALSE;

	/**
	 * @var array
	 */
	protected $paths = [];

	/**
	 * FileSystem constructor.
	 *
	 * @param array $paths
	 */
	public function __construct( $paths = [] ) {
		if ( !empty( $paths ) ) {
			$this->in( $paths );
		}
	}

    /**
     * @inheritDoc
     */
    static public function create( ContainerInterface $container ) {
        return new static();
    }

	/**
	 * Normalize an array of paths.
	 *
	 * @param array $paths
	 *
	 * @return array
	 */
	public function normalizePaths( $paths ) {
		return array_map( function( $path ) {
			return rtrim( $path, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
		}, (array) $paths );
	}

	/**
	 * @param array $paths
	 *
	 * @return $this
	 */
	public function in( $paths = [] ) {
		$paths = $this->normalizePaths( $paths );
		$this->paths = $paths;
		return $this;
	}

	/**
	 * Toggle recursive searching.
	 *
	 * @param bool $recurse
	 *
	 * @return $this
	 */
	public function recurse( $recurse = TRUE ) {
		$this->recursive = $recurse;
		return $this;
	}

	/**
	 * Get a list of files matching given patterns.
	 *
	 * @param string[] $matchPatterns
	 * @param string[] $noMatchPatterns
	 *
	 * @return \Kinglet\FileSystem\FileFilterIterator
	 */
	public function files( $matchPatterns = [], $noMatchPatterns = [] ) {
		if ( empty( $this->paths ) ) {
			throw new \RuntimeException( __( 'Must define paths to find files in.' ) );
		}

		$iterator = $this->getIterator();
		return new FileFilterIterator( $iterator, (array) $matchPatterns, $noMatchPatterns );
	}

	/**
	 * Get a list of directories matching given patterns.
	 *
	 * @param string[] $matchPatterns
	 * @param string[] $noMatchPatterns
	 *
	 * @return \Kinglet\FileSystem\FileFilterIterator
	 */
	public function dirs( $matchPatterns = [], $noMatchPatterns = [] ) {
		if ( empty( $this->paths ) ) {
			throw new \RuntimeException( __( 'Must define paths to find files in.' ) );
		}

		$iterator = $this->getIterator();
		return new FileFilterIterator( $iterator, (array) $matchPatterns, $noMatchPatterns, 2 );
	}

	/**
	 * Get appropriate iterator for all paths.
	 *
	 * @return \AppendIterator
	 */
	public function getIterator() {
		$iterator = new \AppendIterator();
		if ( $this->recursive ) {
			foreach ( $this->paths as $path ) {
				$iterator->append( new \RecursiveIteratorIterator(
					new \RecursiveDirectoryIterator( $path )
				) );
			}

			return $iterator;
		}

		foreach ( $this->paths as $path ) {
			$iterator->append( new \DirectoryIterator( $path ) );
		}
		return $iterator;
	}

}
