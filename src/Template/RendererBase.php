<?php

namespace Kinglet\Template;

abstract class RendererBase implements RendererInterface {

	/**
	 * @var array
	 */
	protected $default_options = [];

	/**
	 * @var array
	 */
	protected $options = [];

	/**
	 * Renderer constructor.
	 *
	 * @param array $options
	 */
	public function __construct( $options = [] ) {
		$this->setOptions( $options );
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
		$this->options = array_replace( $this->default_options, $options );
	}

	/**
	 * {@inheritDoc}
	 */
	public function resetOptions() {
		$this->options = $this->default_options;
	}

}
