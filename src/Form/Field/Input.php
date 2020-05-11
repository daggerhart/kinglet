<?php

namespace Kinglet\Form\Field;

use Kinglet\Form\FieldBase;

class Input extends FieldBase {

	/**
	 * {@inheritDoc}
	 */
	public function name() {
		return 'input';
	}

	/**
	 * {@inheritDoc}
	 */
	public function render( $field ) {
		?>
		<input
			type="<?php echo esc_attr( $field['type'] ) ?>"
			name="<?php echo esc_attr( $field['form_name'] ); ?>"
			id="<?php echo esc_attr( $field['id'] ); ?>"
			class="<?php echo esc_attr( $field['class'] ); ?>"
			value="<?php echo esc_attr( $field['value'] ); ?>"
			<?php echo $this->attributes( $field['attributes'] ); ?>
		>
		<?php
	}

}
