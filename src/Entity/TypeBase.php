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
	public function meta( $name ) {
		return get_metadata( $this->type(), $this->id(), $name, true );
	}

}
