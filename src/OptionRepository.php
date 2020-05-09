<?php

namespace Kinglet;

/**
 * Class OptionRepository
 *
 * @package Kinglet
 */
class OptionRepository extends OptionRepositoryImmutable {

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
		return update_option( $this->optionName, $this->values );
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
