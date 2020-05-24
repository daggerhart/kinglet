<?php

namespace Kinglet\Entity\Query;

use Kinglet\Entity\QueryBase;
use Kinglet\Entity\Type\Term;
use Kinglet\Entity\TypeInterface;
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
	 * @inheritDoc
	 */
	public function queryClassName() {
		return WP_Term_Query::class;
	}

	/**
	 * @inheritDoc
	 */
	public function entityClassName() {
		return Term::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute( $callback = null ) {
		$query_class_name = $this->queryClassName();
		$entity_class_name = $this->entityClassName();
		$this->query = new $query_class_name( $this->arguments );

		foreach ( $this->query->get_terms() as $term ) {
			/** @var TypeInterface $item */
			$item = new $entity_class_name( $term );
			$this->results[ $item->id() ] = $item;

			if ( is_callable( $callback ) ) {
				call_user_func( $callback, $item );
			}
		}

		return $this->results;
	}

}
