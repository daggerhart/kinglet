<?php

namespace Kinglet\Form;

interface FieldInterface {

	/**
	 * Unique name for this type of template.
	 *
	 * @return string
	 */
	public function name();

	/**
	 * Generate and output the field HTML.
	 *
	 * @param $field
	 */
	public function render($field );

	/**
	 * Prepare the field for rendering. Modify the field array and return it.
	 *
	 * @param array $field
	 * @param string $name
	 *
	 * @return array
	 */
	public function process( $field, $name );

}
