<?php

namespace Kinglet\Template;

use Kinglet\Container\ContainerInterface;
use Kinglet\Container\ContainerInjectionInterface;

class StringRenderer extends RendererBase implements ContainerInjectionInterface {

	/**
	 * Renderer configuration options.
	 *
	 * @var array
	 */
	protected $default_options = [
		'prefix' => '{{ ',
		'suffix' => ' }}',
	];

	/**
	 * @inheritDoc
	 */
	public static function create( ContainerInterface $container ) {
		return new static();
	}

	/**
	 * Simple string replacement with context key character wrappings.
	 *
	 * @param string $template
	 *   String that acts as a template.
	 * @param array $context
	 *   Key value pairs of template replacement values.
	 *
	 * @return string
	 */
	public function render( $template, $context = [] ) {
		$pairs = $this->replacements( $context );

		return strtr( $template, $pairs );
	}

	/**
	 * Render an array of templates with a single context.
	 *
	 * @param array $templates
	 * @param array $context
	 *
	 * @return array
	 */
	public function renderArray( $templates, $context ) {
		$pairs = $this->replacements( $context );
		$templates = (array) $templates;
		$rendered = [];
		foreach ( $templates as $i => $template ) {
			$rendered[ $i ] = strtr( $template, $pairs );
		}

		return $rendered;
	}

	/**
	 * Create the replacement pairs for rendering.
	 *
	 * @param array $context
	 *
	 * @return array
	 */
	protected function replacements( $context ) {
		$pairs = [];
		foreach ( $context as $key => $value ) {
			$pairs[ $this->options['prefix'] . $key . $this->options['suffix'] ] = $value;
		}

		return $pairs;
	}

}
