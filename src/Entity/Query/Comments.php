<?php

namespace Kinglet\Entity\Query;

use Kinglet\Entity\QueryBase;
use Kinglet\Entity\Type\Comment;
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
	 * {@inheritdoc}
	 */
	public function execute( $callback = null ) {
		$this->query = new WP_Comment_Query( $this->arguments );

		foreach ( $this->query->get_comments() as $comment ) {
			$item = new Comment( $comment );
			$this->results[ $item->id() ] = $item;

			if ( is_callable( $callback ) ) {
				call_user_func( $callback, $item );
			}
		}

		return $this->results;
	}

}
