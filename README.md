# Kinglet 

WordPress plugin base classes for administration pages.

## Admin PageBase

Base class meant to be inherited to create custom admin pages.

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

### Registering admin pages in a plugin.

```php
add_action( 'admin_menu', function() {
	require_once 'parent-page.php';
	require_once 'child-page.php';

	$parentPage = new MyParentPage();
	$parentPage->addToMenu();

	$childPage = new MyChildPage();
	$childPage->addToSubMenu($parentPage);
} );
```

## Finder

File system browser for locating files and directories. Very-inspired by symfony/finder.

```php
// Define paths when instantiating the Finder object.
$finder = new \Kinglet\FileSystem\Finder([
	get_stylesheet_directory(),
]);

// Find files using the files() method.
// Search with an array of patterns to match and/or not-match.
$files = $finder->files(['*.php'], ['func*', 'index*', '404*']);

/** @var \SplFileInfo $fileinfo */
foreach ($files as $fileinfo) {
	echo $fileinfo->getRealPath() . '<br>';
}

// Or set paths with the in() method.
$dirs = $finder->in(ABSPATH . 'wp-content/uploads')->recurse()->dirs();

/** @var \SplFileInfo $fileinfo */
foreach ($dirs as $fileinfo) {
	echo $fileinfo->getRealPath() . '<br>';
}
```

## Template Engine

Simple templating system.

```php
$engine = new Kinglet\Template\Engine( [
	'paths' => [ __DIR__ ],
	'extension' => '.html.php',
] );

echo $engine->render( 'template-example', [
	'title' => 'Hello',
	'content' => 'World',
] );

/*
 * Provide an array of possible template names, ordered from least specific to most specific.
 * The engine will render the most specific template found.
 */
$suggestions = [
	'template-example',
	'template-first-match',
];
echo $engine->render( $suggestions, [
	'title' => 'My Title',
	'content' => 'My special content',
] );

// Render strings as simple templates.
$string = <<<EOF
<div>
	<h2>{{ my_h2 }}</h2>
	<p>{{ some_content }}</p>
</div>
EOF;

echo $engine->renderString( $string, [
	'my_h2' => 'Example string rendering',
	'some_content' => 'You can use a string as a template.'
] );

// Change the simple replacement delimiters expected by renderString()
$string = '
<div>
	<h2>___my_h2|||</h2>
	<p>___some_content|||</p>
</div>
';
$engine->setOptions([
	'string_context_prefix' => '___',
	'string_context_suffix' => '|||',
]);
echo $engine->renderString( $string, [
	'my_h2' => 'Another Example',
	'some_content' => 'That changes the context key delimiters.'
] );

// Render a callable as a template.
echo $engine->renderCallable( function( $context ) {
	?>
	<div>
		<h2><?= $context['title'] ?></h2>
		<p><?= $context['content'] ?></p>
	</div>
	<?php
}, [
	'title' => 'My Title',
	'content' => 'My content.'
] );
```

## Registry

Registries are simple value stores.

```php
// Registry is a simple data store mechanism.
$registry = new \Kinglet\Registry();
$registry->set('key', 'value');
echo $registry->get('key');

// It can act like an array.
$registry = new \Kinglet\Registry( [
	'first' => 1,
	'second' => 2,
	'another' => 3,
] );
foreach ( $registry as $key => $value ) {
	echo "key: {$key} => value: {$value}" . '<br>';
}
var_dump( $registry->all() );
```

### Discoverable Interface Registry

Discoverable Interface Registry is used to find PHP classes of a certain interface.

The discovery sources (file paths) are loaded with a WordPress filter name you define when instantiating the registry.

```php
$registry = new \Kinglet\DiscoverableInterfaceRegistry(
	'Kinglet\Template\TemplateCallableInterface',
	'name',
	'template-callables'
);

add_filter( 'template-callables', function( $sources) {
	$sources['Kinglet\Template\Field'] = ABSPATH . 'wp-content/plugins/_dev/vendor/daggerhart/kinglet/src/Template/Field';
	$sources['Kinglet\Template\Element'] = ABSPATH . 'wp-content/plugins/_dev/vendor/daggerhart/kinglet/src/Template/Element';
	return $sources;
} );

var_dump($registry->all());
```

## Option Repository

Object wrapper for a WordPress option.

```php
$repo = new \Kinglet\Storage\OptionRepository( 'my_plugin_settings', [
	'key_1' => 'a',
	'key_2' => 'b',
	'another_key' => 1,
] );

// Get values, replace with new values.
echo $repo->get('key_1') . '<br>';

// Replace with new values.
$repo->set('key_1', 'new value for key 1');
echo $repo->get('key_1') . '<br>';

// Remove values.
$repo->unset('key_1');

// Loop through all values.
foreach ( $repo as $name => $value ) {
	echo "{$name} = {$value}" . '<br>';
}

// Save the option to the database.
$repo->save();

// Default values only apply if no database values are found during instantiation.
$repo = new \Kinglet\Storage\OptionRepository( 'my_plugin_settings', [
	'key_1' => 'a',
	'key_2' => 'b',
	'another_key' => 1,
] );

// Loop through all values.
foreach ( $repo as $name => $value ) {
	echo "{$name} = {$value}" . '<br>';
}

// Delete the option from the database
$repo->delete();
```
