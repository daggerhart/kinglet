<?php

namespace Kinglet\Entity\Query;

use Kinglet\Entity\QueryBase;
use Kinglet\Entity\Type\User;
use Kinglet\Entity\TypeInterface;
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
	 * @inheritDoc
	 */
	public function getQueryClass() {
		return WP_User_Query::class;
	}

	/**
	 * @inheritDoc
	 */
	public function getEntityClass() {
		return User::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute( $callback = null ) {
		$query_class_name = $this->getQueryClass();
		$entity_class_name = $this->getEntityClass();
		$this->query = new $query_class_name( $this->arguments );

		foreach ( $this->query->get_results() as $user ) {
			/** @var TypeInterface $item */
			$item = new $entity_class_name( $user );
			$this->results[ $item->id() ] = $item;

			if ( is_callable( $callback ) ) {
				call_user_func( $callback, $item );
			}
		}

		return $this->results;
	}

}
