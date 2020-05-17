<?php

namespace Kinglet\Form;

use Kinglet\Registry\DiscoverableInterfaceRegistry;
use Kinglet\Template\RendererInterface;
use RuntimeException;

/**
 * Class Form
 */
class Form {

	use TraitAttributes;

	/**
	 * Necessary argument defaults for a working form
	 *
	 * @var array
	 */
	protected $defaultFormOptions = [
		// Unique ID for this form instance, and assigned as id on form element.
		'id' => '',
		// Additional classes to add the to the form element.
		'class' => [],
		// Request method. POST|GET
		'method' => 'POST',
		// Form action URL.
		'action' => '',
		// Prefix all field names with this value.
		'form_prefix' => 'kinglet',
		// Include the <form> element during rendering.
		'form_element' => true,
		// Additional form_element attributes.
		'attributes' => [],
		// Form Style name.
		'style' => 'flat',
		// Array of field definitions for this form.
		'fields' => [],
	];

	/**
	 * Form settings (arguments). Combination of arguments provided to the
	 * constructor and the default arguments
	 *
	 * @var array
	 */
	protected $formOptions = [];

	/**
	 * Form Styles
	 *
	 * @var DiscoverableInterfaceRegistry
	 */
	protected $formStyleManager;

	/**
	 * @var FormStyleInterface
	 */
	protected $formStyle;

	/**
	 * Field Types
	 *
	 * @var DiscoverableInterfaceRegistry
	 */
	protected $fieldTypeManager;

	/**
	 * @var FieldInterface[]
	 */
	protected $fieldTypeInstances = [];

	/**
	 * @var RendererInterface
	 */
	protected $renderer;

	/**
	 *  Necessary argument defaults for a working field
	 *
	 * @var array
	 */
	protected $defaultFieldArgs = [
		'title' => '',
		'description' => '',
		'help' => '',
		'type' => 'text',
		'class' => [],
		'value' => '',
		'name' => '',
		'label_first' => true,
		'access' => true,

		// [top-lvl][mid-lvl][bottom-lvl]
		'name_prefix' => '',
		// additional special attributes like size, rows, cols, etc
		'attributes' => [],
		// only for some field types
		'options' => [],

		// Generated automatically
		'form_name' => '',
		'id' => '',
	];

	/**
	 * Array of field definitions.
	 *
	 * @var array
	 */
	protected $fields = [];

	/**
	 * Form constructor.
	 *
	 * @param array $form_options
	 * @param RendererInterface $renderer
	 * @param DiscoverableInterfaceRegistry $form_styles
	 * @param DiscoverableInterfaceRegistry $field_types
	 */
	public function __construct( $form_options, RendererInterface $renderer, DiscoverableInterfaceRegistry $form_styles, DiscoverableInterfaceRegistry $field_types ) {
		$this->setOptions( $form_options );
		$this->setFields( $this->formOptions['fields'] );
		$this->renderer = $renderer;
		$this->formStyleManager = $form_styles;
		$this->fieldTypeManager = $field_types;
	}

	/**
	 * @param array $form_options
	 */
	public function setOptions( $form_options = [] ) {
		$this->formOptions = array_replace( $this->defaultFormOptions, $form_options );
	}

	/**
	 * Get the fields array.
	 *
	 * @return array
	 */
	public function getFields() {
		return $this->fields;
	}

	/**
	 * Set all new fields array.
	 *
	 * @param $fields
	 */
	public function setFields( $fields ) {
		$this->fields = $fields;
	}

	/**
	 * Modify fields recursively.
	 *
	 * @param $fields
	 */
	public function modifyFields( $fields ) {
		$this->fields = array_replace_recursive( $this->fields, $fields );
	}

	/**
	 * Retrieve the current form_style array
	 *
	 * @param $style_name
	 *
	 * @return FormStyleInterface
	 */
	function getFormStyle( $style_name ) {
		if ( ! $this->formStyle && $this->formStyleManager->has( $style_name ) ) {
			$style = $this->formStyleManager->get( $style_name );
			$this->formStyle = new $style();
		}

		return $this->formStyle;
	}

	/**
	 * Get the class instance for a named field type.
	 *
	 * @param string $type
	 *
	 * @return FieldInterface
	 */
	function getFieldTypeInstance( $type ) {
		if ( ! $this->fieldTypeInstances[ $type ] ) {
			if ( $this->fieldTypeManager->has( $type ) ) {
				$field_type = $this->fieldTypeManager->get( $type );
				$this->fieldTypeInstances[ $type ] = new $field_type();
			} else {
				throw new RuntimeException( __( 'Field type not found: ' . $type ) );
			}
		}

		return $this->fieldTypeInstances[ $type ];
	}

	/**
	 * Opening form html and form style html.
	 *
	 * @return string
	 */
	public function open() {
		$style = $this->getFormStyle( $this->formOptions['style'] );
		$attributes = $this->attributes( array_replace( [
			'id' => $this->formOptions['id'] ? $this->formOptions['id'] : $this->formOptions['form_prefix'],
			'action' => $this->formOptions['action'],
			'method' => $this->formOptions['method'],
			'class' => $this->formOptions['class'],
		], $this->formOptions['attributes'] ) );

		ob_start();
		if ( $this->formOptions['form_element'] ) {
			echo "<form {$attributes}>";
		}
		$style->open( [
			'class' => [
				'form--' . $this->formOptions['id'],
				'form-style--' . $style->type()
			],
		] );

		return ob_get_clean();
	}

	/**
	 * Closing form html and form style html.
	 *
	 * @return string
	 */
	public function close() {
		$style = $this->getFormStyle( $this->formOptions['style'] );

		ob_start();
		$style->close();
		if ( $this->formOptions['form_element'] ) {
			echo "</form>";
		}

		return ob_get_clean();
	}

	/**
	 * Render an entire form that is instantiated with fields.
	 *
	 * @return string
	 */
	public function render() {
		$output = $this->open();

		foreach ( $this->getFields() as $name => $field ) {
			$field = $this->processField( $field, $name );

			// Do not render fields users do not have access to.
			if ( $field['access'] ) {
				$output .= $this->renderField( $field );
			}
		}

		$output .= $this->close();

		return $output;
	}

	/**
	 * Execute the filters and methods that render a field
	 *
	 * @param $field
	 *
	 * @return string
	 */
	public function renderField( $field ) {
		// Template the field.
		$field_type = $this->getFieldTypeInstance( $field['type'] );
		$field_html = $this->renderer->render( [ $field_type, 'render' ], [ 'field' => $field ] );

		// Template the wrapper.
		$style = $this->getFormStyle( $this->formOptions['style'] );

		return $this->renderer->render( [ $style, 'fieldWrapper' ], [
			'field' => $field,
			'field_html' => $field_html,
		] );
	}

	/**
	 * Prepare the field array for templating.
	 *
	 * @param array $field
	 * @param string $name
	 *
	 * @return array
	 */
	function processField( $field, $name ) {
		$field = array_replace( $this->defaultFieldArgs, $field );
		if ( empty( $field['name'] ) ) {
			$field['name'] = $name;
		}
		$field['name'] = sanitize_title( $field['name'] );

		// Allow the field type a chance to alter field data.
		$field_type = $this->getFieldTypeInstance( $field['type'] );
		$field = $field_type->process( $field, $name );

		// build the field's entire form name
		$field['form_name'] = '';
		if ( ! empty( $this->formOptions['form_prefix'] ) ) {
			$field['form_name'] .= $this->formOptions['form_prefix'];
		}
		if ( ! empty( $field['name_prefix'] ) ) {
			$field['form_name'] .= $field['name_prefix'];
		}
		if ( ! empty( $field['form_name'] ) ) {
			$field['form_name'] .= '[' . $field['name'] . ']';
		} else {
			$field['form_name'] .= $field['name'];
		}

		// gather field classes
		if ( ! is_array( $field['class'] ) ) {
			$field['class'] = [ $field['class'] ];
		}
		$field['class'][] = 'field';
		$field['class'][] = 'field-' . $this->formOptions['style'];
		$field['class'][] = 'field-type-' . $field['type'];
		$field['class'] = implode( ' ', $field['class'] );

		if ( empty( $field['id'] ) ) {
			$field['id'] = implode( '--', [
				'edit',
				sanitize_title( $field['name'] )
			] );
		}

		if ( ! empty( $field['required'] ) ) {
			$field['attributes']['required'] = true;
		}

		return $field;
	}

	/**
	 * Retrieve submitted values from the request by form_prefix.
	 *
	 * @return array
	 */
	public function getSubmittedValues() {
		$request = $this->formOptions['method'] == 'POST' ? $_POST : $_GET;

		if ( ! empty( $this->formOptions['form_prefix'] ) && isset( $request[ $this->formOptions['form_prefix'] ] ) ) {
			return $request[ $this->formOptions['form_prefix'] ];
		}

		return [];
	}

	/**
	 * Get the value of a form field from the submitted array, by using
	 *  the field's complete "name" attribute.
	 *
	 * @param $form_name - string of field form name
	 *                   - like: some-prefix[top][middle][another]
	 * @param $data - an array of submitted data
	 *
	 * @return array
	 */
	function getFieldValueFromData( $form_name, $data ) {
		$temp = explode( '[', $form_name );
		$keys = array_map( 'sanitize_title_with_dashes', $temp );

		return $this->arrayQuery( $keys, $data );
	}

	/**
	 * Using an array of keys as a path, find a value in a multi-dimensional
	 * array
	 *
	 * @param $keys
	 * @param $data
	 *
	 * @return mixed|null
	 */
	function arrayQuery( $keys, $data ) {
		if ( empty( $keys ) ) {
			return $data;
		}

		$key = array_shift( $keys );

		if ( isset( $data[ $key ] ) ) {

			// if this was the last key, we have found the value
			if ( empty( $keys ) ) {
				return $data[ $key ];
			}
			// if there are remaining keys and this key leads to an array,
			// recurse using the remaining keys
			else if ( is_array( $data[ $key ] ) ) {
				return $this->arrayQuery( $keys, $data[ $key ] );
			} // there are remaining keys, but this item is not an array
			else {
				return null;
			}
		}

		return null;
	}

}
