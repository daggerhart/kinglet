<?php

namespace Kinglet\Form\Style;

use Kinglet\Form\FormStyleBase;

class Box extends FormStyleBase {

	/**
	 * @inheritDoc
	 */
	public function name() {
		return 'box';
	}

	/**
	 * @inheritDoc
	 */
	public function fieldWrapper( $field, $field_html ) {
		?>
		<div
			id="<?php echo esc_attr( $field['id'] ) ;?>--wrapper"
		    class="field-wrapper field-wrapper-box box">

			<h3>
				<label for="<?php echo esc_attr( $field['id'] ); ?>" class="field-label">
					<?php echo $field['title']; ?>
				</label>
			</h3>

			<div>
				<?php if ( !empty( $field['description'] ) ) : ?>
					<p class="description"><?php echo $field['description']; ?></p>
				<?php endif; ?>

				<?php echo $field_html; ?>

				<?php if ( !empty( $field['title'] ) && !$field['label_first']) : ?>
					<label for="<?php echo esc_attr( $field['id'] ); ?>" class="field-label">
						<?php echo $field['title']; ?>
					</label>
				<?php endif; ?>

				<?php if ( !empty($field['help']) ) : ?>
					<p class="description"><?php echo $field['help']; ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
