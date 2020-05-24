<?php

namespace Kinglet\Entity\Query;

use Kinglet\Entity\QueryBase;
use Kinglet\Entity\Type\Comment;
use Kinglet\Entity\TypeInterface;
use WP_Comment_Query;

/**
 * Class Comments
 *
 * @package Kinglet\Entity\Query
 */
class Comments extends QueryBase {

	/**
	 * {@inheritdoc}
	 */
	public function type() {
		return 'comment';
	}

	/**
	 * @inheritDoc
	 */
	public function queryClassName() {
		return WP_Comment_Query::class;
	}

	/**
	 * @inheritDoc
	 */
	public function entityClassName() {
		return Comment::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute( $callback = null ) {
		$query_class_name = $this->queryClassName();
		$entity_class_name = $this->entityClassName();
		$this->query = new $query_class_name( $this->arguments );

		foreach ( $this->query->get_comments() as $comment ) {
			/** @var TypeInterface $item */
			$item = new $entity_class_name( $comment );
			$this->results[ $item->id() ] = $item;

			if ( is_callable( $callback ) ) {
				call_user_func( $callback, $item );
			}
		}

		return $this->results;
	}

}
