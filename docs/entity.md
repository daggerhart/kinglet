# Entity

Normalize WordPress objects into a shared type of thing with knowable methods.

## Type

All types share the same method names to retrieve shared data.

```php
$typesRegistry = new \Kinglet\DiscoverableInterfaceRegistry(
    'Kinglet\Entity\TypeInterface',
    'type',
    'example-types'
);
add_filter( 'example-types', function ( $sources ) {
    $sources['\Kinglet\Entity\Type'] = __DIR__ . '/../src/Entity/Type';
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

## Query

With shared types we can now share query interfaces.

```php
$queryRegistry = new \Kinglet\DiscoverableInterfaceRegistry(
    'Kinglet\Entity\QueryInterface',
    'type',
    'example-queries'
);
add_filter( 'example-queries', function ( $sources ) {
    $sources['\Kinglet\Entity\Query'] = __DIR__ . '/../src/Entity/Query';
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

