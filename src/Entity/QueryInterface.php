<?php

namespace Kinglet\Entity;

interface QueryInterface {

	/**
	 * Unique string that identifies the type of object this query creates.
	 *
	 * @return string
	 */
	public function type();

	/**
	 * Execute a query and return normalized results.
	 *
	 * @param null|callable $callback
	 *
	 * @return array
	 */
	public function execute( $callback = null );

	/**
	 * Get the original core *_Query object used.
	 *
	 * @return mixed
	 */
	public function query();

	/**
	 * Get all results from the query.
	 *
	 * @return mixed
	 */
	public function results();

	/**
	 * Set the arguments array.
	 *
	 * @param array $arguments
	 */
	public function setArguments( array $arguments );

}
