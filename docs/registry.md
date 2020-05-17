# Registry

Registry is a generic object that stores values and is iterable.

```php
<?php
// Registry is a simple data store mechanism.
$registry = new \Kinglet\Registry\Registry();
$registry->set('key', 'value');
echo $registry->get('key');

// It can act like an array.
$registry = new \Kinglet\Registry\Registry( [
    'first' => 1,
    'second' => 2,
    'another' => 3,
] );
foreach ( $registry as $key => $value ) {
    echo "key: {$key} => value: {$value}" . '<br>';
}
var_dump( $registry->all() );
```

## Discoverable Interface Registry

A discoverable interface registry is a Registry object can find classes that implement a specific interface.

The discovery sources (file paths) are loaded with a WordPress filter name you define when instantiating the registry.

```php
<?php
/*
 * Discoverable Interface Registry is used to find PHP classes.
 */
$registry = new \Kinglet\Registry\DiscoverableInterfaceRegistry(
    // Namespaced interface to discover. 
    'MyPlugin\CustomInterfaceName',
    // (optional) Name of method defined by interface that uniquely identifies an implementation.
    'name',
    // (optional) WordPress filter name for registering file system sources.
    'my-custom-filter-name'
);

// Any other plugin can hook into this filter and define their sources.
add_filter( 'my-custom-filter-name', function( $sources) {
    $sources['MyPlugin\MyImplementationClass'] = __DIR__ . '/src';
    return $sources;
} );

var_dump($registry->all());
```
