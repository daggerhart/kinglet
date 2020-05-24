<?php

namespace Kinglet\Entity\Query;

use Kinglet\Entity\QueryBase;
use Kinglet\Entity\Type\Post;
use Kinglet\Entity\TypeInterface;
use WP_Query;

/**
 * Class Posts
 *
 * @package Kinglet\Entity\Query
 */
class Posts extends QueryBase {

	/**
	 * {@inheritdoc}
	 */
	public function type() {
		return 'post';
	}

	/**
	 * @inheritDoc
	 */
	public function queryClassName() {
		return WP_Query::class;
	}

	/**
	 * @inheritDoc
	 */
	public function entityClassName() {
		return Post::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute( $callback = null ) {
		$arguments = $this->remapKeys( $this->arguments, [
			'number' => 'posts_per_page',
			'include' => 'posts__in',
			'exclude' => 'posts__not_in',
			'search' => 's',
		] );

		$query_class_name = $this->queryClassName();
		$entity_class_name = $this->entityClassName();
		$this->query = new $query_class_name( $arguments );

		if ( $this->query->have_posts() ) {
			while ( $this->query->have_posts() ) {
				$this->query->the_post();
				/** @var TypeInterface $item */
				$item = new $entity_class_name( get_post() );
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
