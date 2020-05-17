<?php

namespace Kinglet\Form\Field;

use Kinglet\Form\FieldBase;

class Select extends FieldBase {

	/**
	 * {@inheritDoc}
	 */
	public function type() {
		return 'select';
	}

	/**
	 * Expects an array of options as $field['options']
	 *
	 * {@inheritDoc}
	 */
	public function render( $field ) {
		?>
		<select
			name="<?php echo esc_attr( $field['form_name'] ); ?>"
			id="<?php echo esc_attr( $field['id'] ); ?>"
			class="<?php echo esc_attr( $field['class'] ); ?>"
			<?php echo $this->attributes( $field['attributes'] ); ?> >
			<?php foreach ( $field['options'] as $value => $option ) : ?>
				<option
					value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $field['value'] ); ?>><?php echo esc_html( $option ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

}
