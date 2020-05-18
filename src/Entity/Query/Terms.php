<?php

namespace Kinglet\Entity\Query;

use Kinglet\Entity\QueryBase;
use Kinglet\Entity\Type\Term;
use WP_Term_Query;

/**
 * Class Terms
 *
 * @package Kinglet\Entity\Query
 */
class Terms extends QueryBase {

	/**
	 * {@inheritdoc}
	 */
	public function type() {
		return 'term';
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute( $callback = null ) {
		$this->query = new WP_Term_Query( $this->arguments );

		foreach ( $this->query->get_terms() as $term ) {
			$item = new Term( $term );
			$this->results[ $item->id() ] = $item;

			if ( is_callable( $callback ) ) {
				call_user_func( $callback, $item );
			}
		}

		return $this->results;
	}

}
