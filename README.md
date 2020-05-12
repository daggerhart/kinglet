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

## Template Renderer

Simple templating system.

```php

$renderer = new Kinglet\Template\FileRenderer( [
	'paths' => [ __DIR__ ],
	'extension' => '.html.php',
] );

echo $renderer->render( 'template-example', [
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
echo $renderer->render( $suggestions, [
	'title' => 'My Title',
	'content' => 'My special content',
] );

/*
 * String Renderer - Render strings as simple templates.
 */
$renderer = new \Kinglet\Template\StringRenderer();
$string = <<<EOF
<div>
	<h2>{{ my_h2 }}</h2>
	<p>{{ some_content }}</p>
</div>
EOF;

echo $renderer->render( $string, [
	'my_h2' => 'Example string rendering',
	'some_content' => 'You can use a string as a template.'
] );

// Change the simple replacement delimiters expected by renderString()
$renderer->setOptions([
	'prefix' => '||--',
	'suffix' => '--||',
]);
echo $renderer->render( '<strong>||--item_1--||</strong> ||--item_2--||', [
	'item_1' => 'Hello',
	'item_2' => 'World'
] );

/*
 * Callable Renderer - Render a callable as a template.
 */
$renderer = new \Kinglet\Template\CallableRenderer();
echo $renderer->render( function( $title, $content ) {
	?>
	<div>
		<h2><?= $title ?></h2>
		<p><?= $content ?></p>
	</div>
	<?php
}, [
	'title' => 'My Title',
	'content' => 'My content.'
] );

$named_parameters = [
	'third' => 3,
	'first' => 1,
	'second' => 2,
];
echo $renderer->render( function( $second, $third, $first ) {
	?>
	First: <?= $first ?><br>
	Second: <?= $second ?><br>
	Third: <?= $third ?><br>
	<?php
}, $named_parameters );
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
	'Kinglet\Template\FieldInterface',
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

## Form

```php
$form = \Kinglet\Form\Form::create( [
	'field_prefix' => 'my-form',
	'style' => 'box',
] );

$submitted = $form->getSubmittedValues();
var_dump($submitted);

$form->setFields( [
	'first' => [
		'title' => __( 'Hello' ),
		'type' => 'text',
		'value' => $submitted['first'] ?? 'default',
	],
	'second' => [
		'title' => __( 'You like?' ),
		'type' => 'checkbox',
		'value' => $submitted['second'] ?? FALSE,
	],
	'submit' => [
		'value' => __( 'Submit' ),
		'type' => 'submit',
	],
] );

echo $form->render();
```

## Entity

Normalize WordPress objects into a shared type of thing with knowable methods.

### Type

All types share the same method names to retrieve shared data.

```php
	$typesRegistry = new \Kinglet\DiscoverableInterfaceRegistry(
		'Kinglet\Entity\TypeInterface',
		'type',
		'example-types'
	);
	add_filter( 'example-types', function ( $sources ) {
		$sources['\Kinglet\Entity\Type'] = __DIR__ . '/../../src/Entity/Type';
		return $sources;
	} );

	$testMethods = [ 'id', 'title', 'content', 'slug', 'type' ];
	$entities = [];
	$results = [];

	foreach ( $typesRegistry as $key => $class ) {
		$entities[ $key ] = call_user_func( [ $class, 'load' ], 1 );
	}

	foreach ( $entities as $entity ) {
		foreach ( $testMethods as $method ) {
			$results[ get_class( $entity ) ][ $method ] = call_user_func( [
				$entity,
				$method,
			] );
		}
	}

	var_dump($entities);
	var_dump($results);
```

### Query

With shared types we can now share query interfaces.

```php
	$queryRegistry = new \Kinglet\DiscoverableInterfaceRegistry(
		'Kinglet\Entity\QueryInterface',
		'type',
		'example-queries'
	);
	add_filter( 'example-queries', function ( $sources ) {
		$sources['\Kinglet\Entity\Query'] = __DIR__ . '/../vendor/daggerhart/kinglet/src/Entity/Query';
		return $sources;
	} );

	$testMethods = [ 'execute' ];
	$testArguments = [
		'include' => [ 1 ],
		'number' => 100,
	];

	$queries = [];
	$results = [];

	foreach ( $queryRegistry as $key => $class ) {
		$queries[ $key ] = new $class( $testArguments );
	}

	foreach ( $queries as $query ) {
		foreach ( $testMethods as $method ) {
			$results[ get_class( $query ) ][ $method ] = call_user_func( [
				$query,
				$method,
			] );
		}
	}

	var_dump( $queries );
	var_dump( $results );
```

