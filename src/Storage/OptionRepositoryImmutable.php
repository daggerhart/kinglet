<?php

namespace Kinglet\Storage;

/**
 * Class OptionRepositoryImmutable
 *
 * @package Kinglet
 */
class OptionRepositoryImmutable implements \IteratorAggregate, \Countable {

	/**
	 * Value of the option_name column in the wp_options table.
	 *
	 * @var string
	 */
	protected $optionName = '';

	/**
	 * Default values provided during creation.
	 *
	 * @var array
	 */
	protected $defaultValues = [];

	/**
	 * Store of values.
	 *
	 * @var array
	 */
	protected $values = [];

	/**
	 * OptionRepository
	 *
	 * @param string $option_name
	 * @param array $default_values
	 */
	public function __construct( $option_name, $default_values = [] ){
		$this->optionName = $option_name;
		$this->defaultValues = $default_values;
		$this->values = get_option( $this->optionName, $this->defaultValues );
	}

	/**
	 * Get the name of the option.
	 *
	 * @return string
	 */
	public function optionName() {
		return $this->optionName;
	}

	/**
	 * Get the entire option value array.
	 * @return array
	 */
	public function values() {
		return $this->values;
	}

	/**
	 * Determine if the value exists in the store by name.
	 *
	 * @param $key
	 * @return bool
	 */
	public function has( $key ){
		return isset( $this->values[ $key ] );
	}

	/**
	 * Get a value from the store by name.
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function get( $key ){
		if ( $this->has( $key ) ) {
			return $this->values[ $key ];
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getIterator() {
		return new \ArrayIterator( $this->values );
	}

	/**
	 * Counts all the results collected by the iterators.
	 *
	 * @throws \Exception
	 */
	public function count() {
		return iterator_count( $this->getIterator() );
	}

}
