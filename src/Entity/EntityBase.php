<?php

namespace Kinglet\Entity;

/**
 * Class AbstractNormalObject
 *
 * @package Kinglet\Entity\Type
 */
abstract class EntityBase implements TypeObjectInterface {

	/**
	 * Stored object that is being normalized.
	 *
	 * @var object
	 */
	protected $object;

	/**
	 * AbstractNormalObject constructor.
	 *
	 * @param $object
	 *   The object being normalized.
	 */
	public function __construct( $object ) {
		$this->object = $object;
	}

	/**
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function meta( $name ) {
		return get_metadata( $this->type(), $this->id(), $name, true );
	}

}
