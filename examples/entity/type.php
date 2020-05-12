<?php

function() {
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
}
