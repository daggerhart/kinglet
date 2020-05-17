<?php

namespace Kinglet\Form\Field;

use Kinglet\Form\FieldBase;

class Checkboxes extends FieldBase {

	/**
	 * {@inheritDoc}
	 */
	public function type() {
		return 'checkboxes';
	}

	/**
	 * {@inheritDoc}
	 */
	public function render( $field ) {
		$field['class'] .= ' checkboxes-item';
		$i = 0;
		if ( ! is_array( $field['value'] ) ) {
			$field['value'] = array( $field['value'] => $field['value'] );
		}

		foreach ( $field['options'] as $value => $details ) {
			// default to assuming not-array
			$label = $details;
			$description = null;
			$data = null;

			// if array is given, get the title, description, and data
			if ( is_array( $details ) && isset( $details['title'] ) ) {
				$label = $details['title'];

				if ( ! empty( $details['description'] ) ) {
					$description = $details['description'];
				}

				if ( ! empty( $details['data'] ) ) {
					$data = $details['data'];
				}
			}

			?>
			<div class="checkboxes-wrapper">
				<label for="<?php echo esc_attr( $field['id'] ); ?>--<?php echo $i; ?>">
					<input
						type="checkbox"
						name="<?php echo esc_attr( $field['form_name'] ); ?>[<?php echo esc_attr( $value ); ?>]"
						id="<?php echo esc_attr( $field['id'] ); ?>--<?php echo $i; ?>"
						class="<?php echo esc_attr( $field['class'] ); ?>"
						value="<?php echo esc_attr( $value ); ?>"
						<?php checked( isset( $field['value'][ $value ] ) ); ?>
						<?php if ( $data ) {
							print $this->attributes( $data, 'data-' );
						} ?>
					>
					<?php echo $label; ?>
				</label>
				<?php if ( $description ): ?>
					<p class="description"><?php echo $description; ?></p>
				<?php endif; ?>
			</div>
			<?php
			$i ++;
		}
	}

}
