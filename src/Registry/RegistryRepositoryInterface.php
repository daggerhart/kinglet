<?php

namespace Kinglet\Registry;

interface RegistryRepositoryInterface extends RegistryInterface {

	/**
	 * Save the item to the storage system.
	 * If the item does not exist, it should be created.
	 *
	 * @return bool
	 */
	public function save();

	/**
	 * Removes the item from the storage system.
	 *
	 * @return bool
	 */
	public function delete();

}
