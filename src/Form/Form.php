<?php

namespace Kinglet\Form;

use Kinglet\DiscoverableInterfaceRegistry;
use Kinglet\Form\FormStyleInterface;
use Kinglet\Form\FieldInterface;
use Kinglet\Template\CallableRenderer;
use Kinglet\Template\RendererInterface;

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
		'id' => '',
		'class' => [],
		'method' => 'POST',
		'action' => '',
		'attributes' => [],
		'style' => 'flat',
		'form_prefix' => 'kinglet',
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
	protected $formStyles;

	/**
	 * @var FormStyleInterface
	 */
	protected $formStyle;

	/**
	 * Field Types
	 *
	 * @var DiscoverableInterfaceRegistry
	 */
	protected $fieldTypes;

	/**
	 * @var FieldInterface[]
	 */
	protected $fieldTypesInstances = [];

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
		'label_first' => TRUE,
		'access' => TRUE,

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
	public function __construct( $form_options, RendererInterface $renderer, DiscoverableInterfaceRegistry $form_styles, DiscoverableInterfaceRegistry $field_types  ) {
		$this->formOptions = array_replace( $this->defaultFormOptions, $form_options );
		$this->fields = $this->formOptions['fields'];
		$this->renderer = $renderer;
		$this->formStyles = $form_styles;
		$this->fieldTypes = $field_types;
	}

	/**
	 * Factory for creating a form w/ default renderer and registries.
	 *
	 * @param array $form_options
	 * @param RendererInterface|null $renderer
	 * @param DiscoverableInterfaceRegistry|null $form_styles
	 * @param DiscoverableInterfaceRegistry|null $field_types
	 *
	 * @return Form
	 */
	public static function create( $form_options = [], $renderer = null, $form_styles = null, $field_types = null ) {
		static $default_form_styles_registered = false;
		static $default_field_types_registered = false;
		if ( !$renderer ) {
			$renderer = new CallableRenderer();
		}
		if ( !$form_styles ) {
			$form_styles = new DiscoverableInterfaceRegistry(
				'Kinglet\Form\FormStyleInterface',
				'name',
				'kinglet--form-styles--sources'
			);
			if ( !$default_form_styles_registered ) {
				add_filter( 'kinglet--form-styles--sources', function ($sources) {
					$sources['Kinglet\Form\Style'] = __DIR__ . '/Style';
					return $sources;
				} );
			}
		}
		if ( !$field_types ) {
			$field_types = new DiscoverableInterfaceRegistry(
				'Kinglet\Form\FieldInterface',
				'name',
				'kinglet--field-types--sources'
			);
			if ( !$default_field_types_registered ) {
				add_filter( 'kinglet--field-types--sources', function ($sources) {
					$sources['Kinglet\Form\Field'] = __DIR__ . '/Field';
					$sources['Kinglet\Form\Field\Element'] = __DIR__ . '/Field/Element';
					return $sources;
				} );
			}
		}

		return new self( $form_options, $renderer, $form_styles, $field_types );
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
		if ( !$this->formStyle && $this->formStyles->has( $style_name ) ) {
			$style = $this->formStyles->get( $style_name );
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
		if ( ! $this->fieldTypesInstances[ $type ] ) {
			if ( $this->fieldTypes->has( $type ) ) {
				$field_type = $this->fieldTypes->get( $type );
				$this->fieldTypesInstances[ $type ] = new $field_type();
			}
			else {
				throw new \RuntimeException( __( 'Field type not found: ' .$type ) );
			}
		}

		return $this->fieldTypesInstances[ $type ];
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
		echo "<form {$attributes}>";
		$style->open( [
			'class' => [
				'form--' . $this->formOptions['id'],
				'form-style--' . $style->name()
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
		echo "</form>";
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

		// Do not wrap fields that have no values the wrapper deals with.
		if ( empty( $field['title'] ) && empty( $field['description'] ) && empty( $field['help'] ) ) {
			return $field_html;
		}

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

		if ( $field['type'] == 'checkbox' ) {
			$field['label_first'] = FALSE;
		}

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
			$field['id'] = implode('--',[
				'edit',
				sanitize_title( $field['name'] )
			] );
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

		if ( !empty( $this->formOptions['form_prefix'] ) && isset( $request[ $this->formOptions['form_prefix'] ] ) ) {
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
			}
			// there are remaining keys, but this item is not an array
			else {
				return NULL;
			}
		}

		return NULL;
	}

}
