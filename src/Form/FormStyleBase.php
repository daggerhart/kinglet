<?php

namespace Kinglet\Form;

abstract class FormStyleBase implements FormStyleInterface {

	use TraitAttributes;

	/**
	 * {@inheritDoc}
	 */
	public function fieldWrapper( $field, $field_html ) {
		echo $field_html;
	}

	/**
	 * {@inheritDoc}
	 */
	public function open( $attributes = [] ) {
		echo "<div {$this->attributes( $attributes )}>";
	}

	/**
	 * {@inheritDoc}
	 */
	public function close() {
		echo "</div>";
	}

}
