<?php

namespace Kinglet;

use ArrayIterator;
use Countable;
use Exception;
use IteratorAggregate;

/**
 * Class Registry.
 */
class Registry implements RegistryInterface, IteratorAggregate, Countable {

	/**
	 * Store of values.
	 *
	 * @var array
	 */
	protected $items = [];

	/**
	 * Registry constructor.
	 *
	 * @param array $items
	 */
	public function __construct( array $items = [] ) {
		$this->items = $items;
	}

	/**
	 * {@inheritdoc}
	 */
	public function all() {
		return $this->items;
	}

	/**
	 * {@inheritdoc}
	 */
	public function has( $key ) {
		return isset( $this->items[ $key ] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get( $key ) {
		return $this->has( $key ) ? $this->items[ $key ] : null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function set( $key, $value ) {
		$this->items[ $key ] = $value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function unset( $key ) {
		unset( $this->items[ $key ] );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getIterator() {
		return new ArrayIterator( $this->items );
	}

	/**
	 * Counts all the results collected by the iterators.
	 *
	 * @throws Exception
	 */
	public function count() {
		return iterator_count( $this->getIterator() );
	}

}
