<?php

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
