<?php

namespace Kinglet\Form\Style;

use Kinglet\Form\FormStyleBase;

class Flat extends FormStyleBase {

	/**
	 * @inheritDoc
	 */
	public function type() {
		return 'flat';
	}

	/**
	 * @inheritDoc
	 */
	public function fieldWrapper( $field, $field_html ) {
		?>
		<div
			id="<?php echo esc_attr( $field['id'] ); ?>--wrapper"
			class="field-wrapper field-wrapper-flat">

			<?php if ( ! empty( $field['title'] ) && $field['label_first'] ) : ?>
				<label for="<?php echo esc_attr( $field['id'] ); ?>" class="field-label">
					<?php echo $field['title']; ?>
				</label>
				<?php if ( ! empty( $field['required'] ) ) : ?>
					<span class="required">*</span>
				<?php endif ?>
			<?php endif; ?>

			<?php if ( ! empty( $field['description'] ) ) : ?>
				<p class="description"><?php echo $field['description']; ?></p>
			<?php endif; ?>

			<?php echo $field_html; ?>

			<?php if ( ! empty( $field['title'] ) && ! $field['label_first'] ) : ?>
				<label for="<?php echo esc_attr( $field['id'] ); ?>" class="field-label">
					<?php echo $field['title']; ?>
				</label>
			<?php endif; ?>

			<?php if ( ! empty( $field['help'] ) ) : ?>
				<p class="description"><?php echo $field['help']; ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

}
