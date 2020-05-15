# Form

Simple form rendering system.

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
