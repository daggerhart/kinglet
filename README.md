# Kinglet 

WordPress plugin base classes for administration pages.

## Example Admin PageBase Use.

```php
<?php

use Kinglet\Admin\PageBase;

class MyAdminPage extends PageBase {
  
    public function slug() {
        return 'my_admin_page';
    }
    
    public function title() {
        return __( 'My Admin Page' );
    }
    
    public function description() {
        return __( 'Example hello world page.' );
    }
    
    public function page() {
        echo 'Hello, World!';
    }
}
```

## Example Use in Plugin

```php
<?php

add_action( 'admin_menu', function() {
	$myAdminPage = new MyAdminPage();
	$myAdminPage->pageHook = add_menu_page(
		$myAdminPage->title(),
		$myAdminPage->menuTitle(),
		$myAdminPage->capability(),
		$myAdminPage->slug(),
		[ $myAdminPage, 'route' ]
	);
} );
```
