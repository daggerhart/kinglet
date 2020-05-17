<?php

namespace Kinglet\Form\Field\Element;

use Kinglet\Form\FieldBase;

class ListItems extends FieldBase {

	/**
	 * {@inheritDoc}
	 */
	public function type() {
		return 'list_items';
	}

	/**
	 * {@inheritDoc}
	 */
	public function render( $field ) {
		$type = ! empty( $field['list_type'] ) ? $field['list_type'] : 'ul';
		?>
		<<?php echo $type ?><?php echo $this->attributes( $field['attributes'] ); ?>>
		<?php foreach ( $field['items'] as $key => $item ) { ?>
			<li class="item"><?php print $item; ?></li>
		<?php } ?>
		</<?php echo $type ?>>
		<?php
	}

}
