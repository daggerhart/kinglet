<?php

namespace Kinglet\Entity\Query;

use Kinglet\Entity\QueryBase;
use Kinglet\Entity\Type\Post;

/**
 * Class Posts
 *
 * @package Kinglet\Entity\Query
 */
class Posts extends QueryBase {

	/**
	 * {@inheritdoc}
	 */
	public function execute( $callback = null ) {
		$arguments = $this->alterArguments( $this->arguments );
		$this->query = new \WP_Query( $arguments );

		if ( $this->query->have_posts() ) {
			while ( $this->query->have_posts() ) {
				$this->query->the_post();
				$item = new Post( get_post() );
				$this->results[ $item->id() ] = $item;

				if ( is_callable( $callback ) ) {
					call_user_func( $callback, $item );
				}
			}
			wp_reset_query();
		}

		return $this->results;
	}

}
