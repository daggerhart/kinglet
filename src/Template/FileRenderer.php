<?php

namespace Kinglet\Template;

use Kinglet\Container\ContainerInterface;
use Kinglet\Container\ContainerInjectionInterface;
use Kinglet\FileSystem\Finder;
use SplFileInfo;

/**
 * Class Renderer
 *
 * @package Kinglet\Template
 */
class FileRenderer extends RendererBase {

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
	 * @var bool|SplFileInfo
	 */
	protected $foundSuggestion = false;

	/**
	 * Renderer configuration options.
	 *
	 * @var array
	 *   paths (array) - Folders to search for templates within.
	 *   theme_search (bool) - Include the theme folders.
	 *   theme_first (bool) - Search the theme folder first.
	 *   extension (string) - File extension with
	 */
	protected $default_options = [
		'paths' => [],
		'recurse' => FALSE,
		'theme_search' => true,
		'theme_first' => true,
		'extension' => '.php',
	];

	/**
	 * Renderer constructor.
	 *
	 * @param array $options
	 */
	public function __construct( $options = [] ) {
		parent::__construct( $options );
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
	 * Locate a template by suggestions and return the rendered output.
	 *
	 * @param array|string $templates
	 *   Desired template filenames ordered from lowest to highest priority.
	 * @param array $context
	 *   Variables to be injected into the context of the template.
	 *
	 * @return string
	 */
	public function render( $templates, $context = [] ) {
		$output = '';
		$template = $this->find( $templates );
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
	 * @return SplFileInfo|false
	 */
	public function find( $suggestions ) {
		$this->suggestions = $this->prepareSuggestions( $suggestions );
		$this->foundSuggestion = false;
		$theme_searched = false;
		$options = $this->getOptions();

		// Search in theme first.
		if ( $options['theme_included'] && $options['theme_first'] ) {
			$this->foundSuggestion = $this->locateInTheme( $this->suggestions );
			$theme_searched = true;
			if ( $this->foundSuggestion ) {
				return $this->foundSuggestion;
			}
		}
		// Search in registered directories.
		$this->foundSuggestion = $this->locateInPaths( $this->suggestions, $options['paths'] );
		if ( $this->foundSuggestion ) {
			return $this->foundSuggestion;
		}
		// Search in theme.
		if ( $options['theme_included'] && ! $theme_searched ) {
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
	 * @return SplFileInfo|false
	 */
	public function locateInPaths( $filenames, $paths = [] ) {
		$files = $this->finder->recurse( $this->options['recurse'] )->in( $paths )->files( $filenames );

		foreach ( $files as $file ) {
			return $file;
		}

		return false;
	}

	/**
	 * Find the first template found within the theme folders.
	 *
	 * @param array|string $filenames
	 *
	 * @return SplFileInfo|false
	 * @see \locate_template()
	 *
	 */
	public function locateInTheme( $filenames ) {
		$files = $this->finder->recurse( $this->options['recurse'] )->in( [
			STYLESHEETPATH,
			TEMPLATEPATH,
			ABSPATH . WPINC . '/theme-compat/',
		] )->files( $filenames );

		foreach ( $files as $file ) {
			return $file;
		}

		return false;
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
		$suggestions = array_map( function ( $suggestion ) {
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
	 * Perform the template rendering.
	 *
	 * @param SplFileInfo $__template
	 * @param array $__context
	 *
	 * @return string
	 */
	private function renderTemplate( $__template, $__context ) {
		ob_start();
		foreach ( $__context as $key => $value ) {
			${$key} = $value;
		}
		include $__template->getRealPath();

		return ob_get_clean();
	}

}
