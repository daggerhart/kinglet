<?php

namespace Kinglet\Form\Field;

use Kinglet\Form\FieldBase;

class Textarea extends FieldBase {

	/**
	 * {@inheritDoc}
	 */
	public function name() {
		return 'textarea';
	}

	/**
	 * {@inheritDoc}
	 */
	public function render( $field ) {
		?>
		<textarea
			name="<?php echo esc_attr( $field['form_name'] ); ?>"
			id="<?php echo esc_attr( $field['id'] ); ?>"
			class="<?php echo esc_attr( $field['class'] ); ?>"
			<?php echo $this->attributes( $field['attributes'] ); ?>
        ><?php echo $this->escape( $field['value'] ); ?></textarea>
		<?php
	}

	/**
	 * Help prevent excessive slashes and potential malicious code
	 *
	 * @param $value
	 *
	 * @return string
	 */
	function escape( $value ) {
		return stripcslashes( esc_textarea( str_replace( "\\", "", $value ) ) );
	}

}
