<?php

namespace Kinglet\Storage;

/**
 * Class OptionRepository
 *
 * @package Kinglet
 */
class OptionRepository extends OptionRepositoryImmutable {

	protected $autoload;

	/**
	 * OptionRepository constructor.
	 *
	 * {@inheritDoc}
	 * @param bool $autoload
	 *   Whether to load the option when WordPress starts up.
	 */
	public function __construct( $option_name, $default_values = [], $autoload = TRUE ) {
		parent::__construct( $option_name, $default_values );
		$this->autoload = $autoload;
	}

	/**
	 * Set a new value in the store by name.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function set( $key, $value ) {
		$this->values[ $key ] = $value;
	}

	/**
	 * Remove a value from the store by name by using the unset() function.
	 *
	 * @param string $key
	 */
	public function unset( $key ){
		unset( $this->values[ $key ]);
	}

	/**
	 * Updates the value of an option that was already added.
	 *
	 * If the option does not exist, it will be created.
	 *
	 * You do not need to serialize values. If the value needs to be serialized,
	 * then it will be serialized before it is inserted into the database.
	 *
	 * @return bool
	 */
	public function save() {
		return update_option( $this->optionName, $this->values, $this->autoload );
	}

	/**
	 * Removes the option from the database by name.
	 *
	 * @return bool
	 */
	public function delete() {
		return delete_option( $this->optionName );
	}

}
