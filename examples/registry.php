<?php

function() {
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

	/*
	 * Discoverable Interface Registry is used to find PHP classes.
	 */
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
}
