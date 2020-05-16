<?php

namespace Kinglet\Entity;

use ArrayIterator;
use Countable;
use Exception;
use IteratorAggregate;

/**
 * Class AbstractNormalQuery
 *
 * @package Kinglet\Entity\Query
 */
abstract class QueryBase implements QueryInterface, IteratorAggregate, Countable {

	/**
	 * Arguments that augment the query results.
	 *
	 * @var array
	 */
	protected $arguments = [];

	/**
	 * The last executed query object.
	 *
	 * @var object
	 */
	protected $query;

	/**
	 * Results of the last query.
	 *
	 * @var array
	 */
	protected $results = [];

	/**
	 * AbstractNormalQuery constructor.
	 *
	 * @param array $arguments
	 */
	public function __construct( array $arguments ) {
		$this->arguments = $arguments;
	}

	/**
	 * {@inheritDoc}
	 */
	public function query() {
		return $this->query;
	}

	/**
	 * {@inheritDoc}
	 */
	public function results() {
		return $this->results;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getIterator() {
		return new ArrayIterator( $this->results );
	}

	/**
	 * Counts all the results collected by the iterators.
	 *
	 * @throws Exception
	 */
	public function count() {
		return iterator_count( $this->getIterator() );
	}

	/**
	 * Adjust the arguments so that it this query type accepts the same common
	 * arguments as the other query types.
	 *
	 * @param array $array
	 * @param array $map
	 *   Pairs that need remapping. Keys are "from", values are "to".
	 *
	 * @return array
	 */
	public function remapKeys( array $array, array $map ) {
		foreach ( $map as $from => $to ) {
			if ( isset( $array[ $from ] ) && empty( $array[ $to ] ) ) {
				$array[ $to ] = $array[ $from ];
				unset( $array[ $from ] );
			}
		}

		return $array;
	}

}
