<?php

function() {
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
}
