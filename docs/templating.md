# Template Renderers

Simple templating system.

## File Templates

```php
<?php
$renderer = new Kinglet\Template\FileRenderer( [
	'paths' => [ __DIR__ . '/templates' ],
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
```

## String Templates

```php
<?php
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
```

## Callables as templates

```php
<?php
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
