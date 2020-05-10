<?php

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
