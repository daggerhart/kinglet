<?php

namespace Kinglet\Entity;

/**
 * Interface TypeObjectInterface
 *
 * @package Kinglet\Entity
 */
interface TypeInterface {

	/**
	 * Create an instance of this type of object from an id.
	 *
	 * @param $id
	 *   The ID for the object.
	 *
	 * @return self
	 */
	static public function load( $id );

	/**
	 * Object type name.
	 *
	 * @return string
	 */
	public function type();

	/**
	 * Object unique identifier.
	 *
	 * @return int
	 */
	public function id();

	/**
	 * Uniquely identifying machine-safe name for the object.
	 *
	 * @return string
	 */
	public function slug();

	/**
	 * Some text that serves as the object content.
	 *
	 * @return string
	 */
	public function content();

	/**
	 * Url for directly accessing the object.
	 *
	 * @return string
	 */
	public function url();

	/**
	 * Getter for specific piece of meta data about the object.
	 *
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function meta( $name );

	/**
	 * Update or create specific piece of meta data about the object.
	 *
	 * @param string $name
	 * @param mixed $value
	 *
	 * @return int|bool
	 *   The new meta field ID if a field with the given key didn't exist and was
	 *   therefore added, true on successful update, false on failure.
	 */
	public function metaUpdate( $name, $value );

	/**
	 * Delete specific piece of meta data about the object.
	 *
	 * @param $name string
	 *
	 * @return bool True on successful delete, false on failure.
	 */
	public function metaDelete( $name );

	/**
	 * Return the raw object being decorated.
	 *
	 * @return mixed
	 */
	public function object();

}
