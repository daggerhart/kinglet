<?php

namespace Kinglet\Entity;

use Kinglet\Entity\Type\User;

/**
 * Interface AuthorInterface
 *
 * @package Kinglet\Entity
 */
interface TypeAuthorInterface {

	/**
	 * @return User
	 */
	public function author();
}
