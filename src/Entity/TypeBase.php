<?php

namespace Kinglet\Entity;

/**
 * Class AbstractNormalObject
 *
 * @package Kinglet\Entity\Type
 */
abstract class TypeBase implements TypeInterface {

	/**
	 * Stored object that is being normalized.
	 *
	 * @var object
	 */
	protected $object;

	/**
	 * TypeBase constructor.
	 *
	 * @param $object
	 *   The object being normalized.
	 */
	public function __construct( $object ) {
		$this->object = $object;
	}

	/**
	 * {@inheritDoc}
	 */
	public function object() {
		return $this->object;
	}

	/**
	 * {@inheritDoc}
	 */
	public function meta( $name, $single = true ) {
		return get_metadata( $this->type(), $this->id(), $name, $single );
	}

	/**
	 * {@inheritDoc}
	 */
	public function metaUpdate( $name, $value, $prev_value = '' ) {
		return update_metadata( $this->type(), $this->id(), $name, $value, $prev_value );
	}

	/**
	 * {@inheritDoc}
	 */
	public function metaDelete( $name, $value = '', $delete_all = false ) {
		return delete_metadata( $this->type(), $this->id(), $name, $value, $delete_all );
	}


}
