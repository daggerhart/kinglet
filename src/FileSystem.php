<?php

namespace Kinglet;

/**
 * Class FileSystem
 *
 * @package Kinglet\Template
 */
class FileSystem {

	protected $paths = [];

	/**
	 * FileSystem constructor.
	 *
	 * @param array $paths
	 */
	public function __construct( $paths = [] ) {
		$this->setPaths( $paths );
	}

	/**
	 * Update the paths this
	 *
	 * @param array $paths
	 */
	public function setPaths( $paths ) {
		$this->paths = $this->normalizePaths( $paths );
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
	 * Locate a template but the stored set of directories, or in the WP theme system.
	 *
	 * @param array|string $filenames
	 * @param array $paths Optionally override the directories searched.
	 *
	 * @return \SplFileInfo|false
	 */
	public function locate( $filenames, $paths = [] ) {
		$paths = !empty( $paths ) ? $paths : $this->paths;
		foreach ( $paths as $path ) {
			foreach ( (array) $filenames as $filename ) {
				if ( file_exists( $path . $filename ) ) {
					return new \SplFileInfo(  $filename  );
				}
			}
		}

		return FALSE;
	}

}
