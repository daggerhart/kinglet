<?php

namespace Kinglet\Form\Style;

use Kinglet\Form\FormStyleBase;

class Table extends FormStyleBase {

	/**
	 * @inheritDoc
	 */
	public function name() {
		return 'table';
	}

	/**
	 * {@inheritDoc}
	 */
	public function open( $attributes = [] ) {
		echo "<table {$this->attributes( $attributes )}>";
	}

	/**
	 * {@inheritDoc}
	 */
	public function close() {
		echo "</table>";
	}

	/**
	 * @inheritDoc
	 */
	public function fieldWrapper( $field, $field_html ) {
		?>
		<tr id="<?php echo esc_attr( $field['id'] ) ;?>--wrapper"
		    class="field-wrapper field-wrapper-table">
			<th scope="row">
				<?php if ( !empty( $field['title'] ) ) : ?>
					<label for="<?php echo esc_attr( $field['id'] ); ?>" class="field-label">
						<?php echo $field['title']; ?>
					</label>
					<?php if ( ! empty( $field['required'] ) ) : ?>
						<span class="required">*</span>
					<?php endif ?>
				<?php endif; ?>
			</th>
			<td>
				<?php echo $field_html; ?>

				<?php if ( !empty( $field['description'] ) ) : ?>
					<p class="description"><?php echo $field['description']; ?></p>
				<?php endif; ?>

				<?php if ( !empty( $field['help'] ) ) : ?>
					<p class="description"><?php echo $field['help']; ?></p>
				<?php endif; ?>
			</td>
		</tr>
		<?php
	}
}
