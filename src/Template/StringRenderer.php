<?php

namespace Kinglet\Template;

class StringRenderer extends RendererBase {

	/**
	 * Renderer configuration options.
	 *
	 * @var array
	 */
	protected $options = [
		'prefix' => '{{ ',
		'suffix' => ' }}',
	];

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
		$keys = array_map( function( $key ) {
			return $this->options['prefix'] . $key . $this->options['suffix'];
		}, array_keys( $context ) );

		return str_replace( $keys, array_values( $context ), $template );
	}
}
