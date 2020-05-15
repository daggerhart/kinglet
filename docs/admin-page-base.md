# Admin PageBase

Base class meant to be inherited to create custom admin pages.

## Examples

```php
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

	public function content() {
		echo 'I am a top-level page';
	}

}
```

```php
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
		echo 'I am a child of the top-level page';
	}

}
```

### Registering admin pages in a plugin.

```php
add_action( 'admin_menu', function() {
	$parentPage = new MyParentPage();
	$parentPage->addToMenu();

	$childPage = new MyChildPage();
	$childPage->addToSubMenu($parentPage);
} );
```
