<?php

namespace Kinglet\Entity\Query;

use Kinglet\Entity\QueryBase;
use Kinglet\Entity\Type\User;

/**
 * Class Users
 *
 * @package Kinglet\Entity\Query
 */
class Users extends QueryBase {

	/**
	 * {@inheritdoc}
	 */
	public function execute( $callback = NULL ) {
		$this->query = new \WP_User_Query( $this->arguments );

		foreach ( $this->query->get_results() as $user ) {
			$item = new User( $user );
			$this->results[ $item->id() ] = $item;

			if ( is_callable( $callback ) ) {
				call_user_func( $callback, $user );
			}
		}

		return $this->results;
	}

}
