<?php

namespace Kinglet\Entity\Query;

use Kinglet\Entity\QueryBase;
use Kinglet\Entity\Type\User;
use WP_User_Query;

/**
 * Class Users
 *
 * @package Kinglet\Entity\Query
 */
class Users extends QueryBase {

	/**
	 * {@inheritdoc}
	 */
	public function type() {
		return 'user';
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute( $callback = null ) {
		$this->query = new WP_User_Query( $this->arguments );

		foreach ( $this->query->get_results() as $user ) {
			$item = new User( $user );
			$this->results[ $item->id() ] = $item;

			if ( is_callable( $callback ) ) {
				call_user_func( $callback, $item );
			}
		}

		return $this->results;
	}

}
