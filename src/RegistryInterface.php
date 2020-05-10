<?php

namespace Kinglet;

interface RegistryInterface {

	/**
	 * Return all items in this registry.
	 *
	 * @return array
	 */
	public function all();

	/**
	 * Determine if the value exists in the store by name.
	 *
	 * @param $key
	 *   The name of the item in the registry.
	 *
	 * @return bool
	 */
	public function has( $key );

	/**
	 * Get a specific item in the registry.
	 *
	 * @param $key
	 *   The name of the item in the registry.
	 *
	 * @return mixed
	 */
	public function get( $key );

	/**
	 * Set a specific item in the registry.
	 *
	 * @param $key
	 *   The name of the item in the registry.
	 * @param $value
	 *   The new value that should represent the item in the registry.
	 *
	 * @void
	 */
	public function set( $key, $value );

	/**
	 * Remove a value from the store by name by using the unset() function.
	 *
	 * @param string $key
	 */
	public function unset( $key );

}
