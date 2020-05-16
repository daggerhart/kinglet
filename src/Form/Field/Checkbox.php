<?php

namespace Kinglet\Form\Field;

use Kinglet\Form\FieldBase;

class Checkbox extends FieldBase {

	/**
	 * {@inheritDoc}
	 */
	public function name() {
		return 'checkbox';
	}

	public function process( $field, $name ) {
		$field['label_first'] = false;

		return parent::process( $field, $name );
	}

	/**
	 * {@inheritDoc}
	 */
	public function render( $field ) {
		$input = new Input();
		$hidden = array_replace( $field, [
			'type' => 'hidden',
			'value' => 0,
			'id' => $field['id'] . '--hidden',
			'attributes' => [],
			'class' => 'field-hidden',
		] );
		$input->render( $hidden );

		if ( ! empty( $field['value'] ) ) {
			$field['attributes']['checked'] = 'checked';
		}
		$field['value'] = 'on';
		$input->render( $field );
	}

}
