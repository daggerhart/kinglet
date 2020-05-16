<?php

namespace Kinglet\Form;

abstract class FieldBase implements FieldInterface {

	use TraitAttributes;

	/**
	 * {@inheritDoc}
	 */
	public function process( $field, $name ) {
		return $field;
	}

}
