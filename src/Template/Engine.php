<?php

namespace Kinglet\Template;

use Kinglet\FileSystem\Finder;

/**
 * Class Renderer
 *
 * @package Kinglet\Template
 */
class Engine implements EngineInterface {

	/**
	 * Utility for locating real files.
	 *
	 * @var Finder
	 */
	protected $finder;

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
		// Prefix for context keys in renderString() method.
		'string_context_prefix' => '{{ ',
		// Suffix for context keys in renderString() method.
		'string_context_suffix' => ' }}',
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
		$this->setFinder( new Finder() );

		if ( !empty( $options ) ) {
			$this->setOptions( $options );
		}
	}

	/**
	 * Set new configuration values.
	 *
	 * @param array $options
	 */
	public function setOptions( $options = [] ) {
		$this->options = array_replace( $this->options, $options );
	}

	/**
	 * Set a new FileSystem for locating templates.
	 *
	 * @param Finder $file_system
	 */
	public function setFinder( Finder $file_system ) {
		$this->finder = $file_system;
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
	 * Find the first template file available amongst the suggestions.
	 *
	 * @param array|string $suggestions
	 *   Desired template filenames ordered from lowest to highest priority.
	 *
	 * @return \SplFileInfo|false
	 */
	public function find( $suggestions ) {
		$this->suggestions = $this->prepareSuggestions( $suggestions );
		$this->foundSuggestion = FALSE;

		// Search in theme first.
		if ( $this->options['theme_included'] && $this->options['theme_first'] ) {
			$this->foundSuggestion = $this->locateInTheme( $this->suggestions );
			if ( $this->foundSuggestion ) {
				return $this->foundSuggestion;
			}
		}
		// Search in registered directories.
		$this->foundSuggestion = $this->locateInPaths( $this->suggestions, $this->options['paths'] );
		if ( $this->foundSuggestion ) {
			return $this->foundSuggestion;
		}
		// Search in theme.
		if ( $this->options['theme_included'] ) {
			$this->foundSuggestion = $this->locateInTheme( $this->suggestions );
		}
		return $this->foundSuggestion;
	}

	/**
	 * Locate the first template found in the given paths.
	 *
	 * @param array|string $filenames
	 * @param array $paths Optionally override the directories searched.
	 *
	 * @return \SplFileInfo|false
	 */
	public function locateInPaths( $filenames, $paths = [] ) {
		$files = $this->finder->in( $paths )->files( $filenames );

		foreach ( $files as $file ) {
			return $file;
		}

		return FALSE;
	}

	/**
	 * Find the first template found within the theme folders.
	 *
	 * @see \locate_template()
	 *
	 * @param array|string $filenames
	 *
	 * @return \SplFileInfo|false
	 */
	public function locateInTheme( $filenames ) {
		$files = $this->finder->in( [
			STYLESHEETPATH,
			TEMPLATEPATH,
			ABSPATH . WPINC . '/theme-compat/',
		] )->files( $filenames );

		foreach ( $files as $file ) {
			return $file;
		}

		return FALSE;
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

	/**
	 * Simple string replacement with context key character wrappings.
	 *
	 * @param string $template
	 *   String that acts as a template.
	 * @param array $context
	 *   Key value pairs of template replacement values.
	 *
	 * @return string|string[]
	 */
	public function renderString( $template, $context = [] ) {
		$keys = array_map( function( $key ) {
			return $this->options['string_context_prefix'] . $key . $this->options['string_context_suffix'];
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

}
