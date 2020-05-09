<?php

namespace Kinglet\Template;

use Kinglet\FileSystem;

/**
 * Class Renderer
 *
 * @package Kinglet\Template
 */
class Renderer implements RendererInterface {

	/**
	 * Utility for locating real files.
	 *
	 * @var \Kinglet\FileSystem
	 */
	protected $fileSystem;

	/**
	 * Store last suggestions searched.
	 *
	 * @var array
	 */
	protected $suggestions = [];

	/**
	 * Last found suggestion.
	 *
	 * @var bool|\SplFileInfo
	 */
	protected $foundSuggestion = false;

	/**
	 * Renderer configuration options.
	 *
	 * @var array
	 */
	protected $options = [
		'paths' => [],
		'theme_search' => TRUE,
		'theme_first' => TRUE,
		'extension' => '.php',
	];

	/**
	 * Renderer constructor.
	 *
	 * @param array $options :
	 *   paths (array) - Folders to search for templates within.
	 *   theme_search (bool) - Include the theme folders.
	 *   theme_first (bool) - Search the theme folder first.
	 *   extension (string) - File extension with
	 */
	public function __construct( $options = [] ) {
		if ( !empty( $options ) ) {
			$this->setOptions( $options );
		}
	}

	/**
	 * Set a new FileSystem for locating templates.
	 *
	 * @param \Kinglet\FileSystem $file_system
	 */
	public function setFileSystem( FileSystem $file_system ) {
		$this->fileSystem = $file_system;
	}

	/**
	 * Get the current renderer configuration.
	 *
	 * @return array
	 */
	public function getOptions() {
		return $this->options;
	}

	/**
	 * Set new configuration values.
	 *
	 * @param array $options
	 */
	public function setOptions( $options = [] ) {
		$this->options = $options;

		if ( !empty( $options['paths'] ) ) {
			$this->setFileSystem( new FileSystem( (array) $options['paths'] ) );
		}
	}

	/**
	 * Locate a template by suggestions and return the rendered output.
	 *
	 * @param array|string $suggestions
	 *   Desired template filenames ordered from lowest to highest priority.
	 * @param array $context
	 *   Variables to be injected into the context of the template.
	 *
	 * @return string
	 */
	public function render( $suggestions, $context = [] ) {
		$output = '';
		$template = $this->find( $suggestions );
		if ( $template ) {
			$output = $this->renderTemplate( $template, (array) $context );
		}

		return $output;
	}

	/**
	 * Simple string replacement with context key character wrappings.
	 *
	 * @param string $template
	 *   String that acts as a template.
	 * @param array $context
	 *   Key value pairs of template replacement values.
	 * @param string $prefix
	 *   Context key prefix identifier.
	 * @param string $suffix
	 *   Context key suffix identifier.
	 *
	 * @return string|string[]
	 */
	public function renderString( $template, $context = [], $prefix = '{{ ', $suffix = ' }}' ) {
		$keys = array_map( function( $key ) use ( $prefix, $suffix ) {
			return $prefix . $key . $suffix;
		}, array_keys( $context ) );

		return str_replace( $keys, array_values( $context ), $template );
	}

	/**
	 * Perform the template rendering.
	 *
	 * @param \SplFileInfo $__template
	 * @param array $__context
	 *
	 * @return string
	 */
	private function renderTemplate( $__template, $__context ) {
		ob_start();
		foreach ( $__context as $key => $value) {
			${$key} = $value;
		}
		include $__template->getRealPath();
		return ob_get_clean();
	}

	/**
	 * Find the first template file available amongst the suggestions.
	 *
	 * @param array|string $suggestions
	 *   Desired template filenames ordered from lowest to highest priority.
	 *
	 * @return \SplFileInfo|false
	 */
	public function find( $suggestions ) {
		if ( !$this->fileSystem instanceof FileSystem ) {
			throw new \RuntimeException( 'FileSystem required for locating templates method.' );
		}
		$this->suggestions = $this->prepareSuggestions( $suggestions );
		$this->foundSuggestion = FALSE;

		// Search in theme first.
		if ( $this->options['theme_included'] && $this->options['theme_first'] ) {
			$this->foundSuggestion = $this->locateInTheme( $suggestions );
			if ( $this->foundSuggestion ) {
				return $this->foundSuggestion;
			}
		}
		// Search in registered directories.
		$this->foundSuggestion = $this->fileSystem->locate( $suggestions );
		if ( $this->foundSuggestion ) {
			return $this->foundSuggestion;
		}
		// Search in theme.
		if ( $this->options['theme_included'] ) {
			$this->foundSuggestion =  $this->locateInTheme( $suggestions );
		}
		return $this->foundSuggestion;
	}

	/**
	 * Find template suggestions within the theme folders.
	 * @see \locate_template()
	 *
	 * @param array|string $suggestions
	 *
	 * @return \SplFileInfo|false
	 */
	public function locateInTheme( $suggestions ) {
		return $this->fileSystem->locate( $suggestions, $this->fileSystem->normalizePaths( [
			STYLESHEETPATH,
			TEMPLATEPATH,
			ABSPATH . WPINC . '/theme-compat/',
		] ) );
	}

	/**
	 * Prepare suggestions for searching.
	 *
	 * @param array $suggestions
	 *
	 * @return array
	 */
	public function prepareSuggestions( $suggestions ) {
		$suggestions = array_reverse( (array) $suggestions );

		$suggestions = array_map( function( $suggestion ) {
			// Append the stored extension to a suggestion if it doesn't have it.
			$extension = substr( $suggestion, strlen( $this->options['extension'] ) );
			if ( strcasecmp( $extension, $this->options['extension'] ) !== 0 ) {
				$suggestion .= $this->options['extension'];
			}
			return $suggestion;
		}, $suggestions );

		return $suggestions;
	}

}
