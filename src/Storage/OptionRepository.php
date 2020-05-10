<?php

namespace Kinglet\Storage;

use Kinglet\Registry;

/**
 * Class OptionRepository
 *
 * @package Kinglet
 */
class OptionRepository extends Registry implements RepositoryInterface {

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
	protected $defaultItems = [];

	/**
	 * Whether to load the option when WordPress starts up.
	 *
	 * @var bool
	 */
	protected $autoload;

	/**
	 * OptionRepository constructor.
	 *
	 * @param string $option_name
	 * @param array $default_values
	 * @param bool $autoload
	 *   Whether to load the option when WordPress starts up.
	 */
	public function __construct( $option_name, $default_values = [], $autoload = TRUE ) {
		$this->optionName = $option_name;
		$this->defaultItems = $default_values;
		$this->setAutoload( $autoload );
		parent::__construct( get_option( $this->optionName, $this->defaultItems ) );
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
	 * Set the autoload status of the option.
	 *
	 * @param $autoload
	 */
	public function setAutoload( $autoload ) {
		$this->autoload = (bool) $autoload;
	}

	/**
	 * {@inheritdoc}
	 */
	public function save() {
		return update_option( $this->optionName, $this->items, $this->autoload );
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete() {
		return delete_option( $this->optionName );
	}

}
