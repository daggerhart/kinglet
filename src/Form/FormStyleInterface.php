<?php

namespace Kinglet\Form;

interface FormStyleInterface {

	/**
	 * Form style unique id.
	 *
	 * @return string
	 */
	public function type();

	/**
	 * Output the field_html within additional elements.
	 *
	 * @param array $field
	 * @param string $field_html
	 */
	public function fieldWrapper( $field, $field_html );

	/**
	 * Output the opening form wrapper HTML.
	 *
	 * @param array $attributes
	 */
	public function open( $attributes );

	/**
	 * Output the closing form wrapper HTML.
	 */
	public function close();

}
