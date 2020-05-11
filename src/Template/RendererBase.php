<?php

namespace Kinglet\Template;

abstract class RendererBase implements RendererInterface {

	protected $options = [];

	/**
	 * Renderer constructor.
	 *
	 * @param array $options
	 */
	public function __construct( $options = [] ) {
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

}
