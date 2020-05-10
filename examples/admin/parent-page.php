<?php

class MyParentPage extends \Kinglet\Admin\PageBase {

	public function slug() {
		return 'my_parent_page';
	}

	public function title() {
		return __( 'My Parent Page' );
	}

	public function description() {
		return __( 'Example hello world page.' );
	}

	public function page() {
		echo 'Hello, World!';
	}

}
