<?php

namespace Kinglet\Entity;

/**
 * Class AbstractNormalQuery
 *
 * @package Kinglet\Entity\Query
 */
abstract class QueryBase implements QueryInterface {

	/**
	 * Arguments that augment the query results.
	 *
	 * @var array
	 */
	public $arguments = array();

	/**
	 * The last executed query object.
	 *
	 * @var object
	 */
	public $query;

	/**
	 * Results of the last query.
	 *
	 * @var array
	 */
	public $results = array();

	/**
	 * AbstractNormalQuery constructor.
	 *
	 * @param array $arguments
	 */
	function __construct( array $arguments ) {
		$this->arguments = $arguments;
	}

	/**
	 * Adjust the arguments so that it this query type accepts the same common
	 * arguments as the other query types.
	 *
	 * @param $arguments array
	 *
	 * @return array
	 */
	public function alterArguments( $arguments ) {
		$map = array(
			'number' => 'posts_per_page',
			'include' => 'posts__in',
			'exclude' => 'posts__not_in',
			'search' => 's',
		);

		foreach ( $map as $from => $to ) {
			if ( isset( $arguments[ $from ] ) && empty( $arguments[ $to ]) ) {
				$arguments[ $to ] = $arguments[ $from ];
				unset( $arguments[ $from ] );
			}
		}

		return $arguments;
	}

}
