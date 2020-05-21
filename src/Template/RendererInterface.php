<?php

namespace Kinglet\Template;

interface RendererInterface {

	/**
	 * Locate a template by suggestions and render it along with the given context.
	 *
	 * @param array $templates
	 * @param array $context
	 *
	 * @return string
	 */
	public function render( $templates, $context = [] );

	/**
	 * Get current renderer options.
	 *
	 * @return array
	 */
	public function getOptions();

	/**
	 * Set new options for the renderer.
	 *
	 * @param array $options
	 */
	public function setOptions( $options = [] );

	/**
	 * Reset the renderer options back to their defaults.
	 */
	public function resetOptions();

}
