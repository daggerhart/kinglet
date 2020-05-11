<?php

namespace Kinglet\Form\Field\Element;

use Kinglet\Form\FieldBase;

class ItemList extends FieldBase {

	/**
	 * {@inheritDoc}
	 */
	public function name() {
		return 'list_items';
	}

	/**
	 * {@inheritDoc}
	 */
	public function render( $context ) {
		$type = !empty( $context['type'] ) ? $context['type'] : 'ul';
		?>
		<<?php echo $type ?> <?php echo $this->attributes( $context['attributes'] ); ?>>
			<?php foreach ( $context['items'] as $key => $item ) { ?>
				<li class="item"><?php print $item; ?></li>
			<?php } ?>
		</<?php echo $type ?>>
		<?php
	}

}
