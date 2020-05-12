<?php

class MyChildPage extends \Kinglet\Admin\PageBase {

	public function slug() {
		return 'my_child_page';
	}

	public function title() {
		return __( 'My Child Page' );
	}

	public function description() {
		return __( 'Example hello world page.' );
	}

	public function content() {
		echo 'Hello, World!';
	}

}
