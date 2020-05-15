<?php

namespace Kinglet\Container;

use Kinglet\Invoker\Invoker;
use Kinglet\Invoker\InvokerInterface;
use Kingley\Container\ContainerInterface;

class Container implements ContainerInterface {

	/**
	 * Items are instances of container classes.
	 *
	 * @var callable[]
	 */
	protected $items = [];

	/**
	 * @var callable[]
	 */
	protected $definitions = [];

	/**
	 * @var \Kinglet\Invoker\InvokerInterface
	 */
	protected $invoker;

	public function __construct( array $definitions = [], InvokerInterface $invoker = NULL ) {
		$this->definitions = $definitions;
		$this->invoker = $invoker ? $invoker : $this->createInvoker();
	}

	/**
	 * Instantiate an invoker.
	 *
	 * @return \Kinglet\Invoker\Invoker
	 */
	protected function createInvoker() {
		return new Invoker();
	}

	/**
	 * {@inheritdoc}
	 */
	public function has( $key ) {
		if ( ! isset( $this->items[ $key ] ) ) {
			return isset( $this->definitions[ $key ] );
		}

		return FALSE;
	}

	/**
	 * {@inheritdoc}
	 * @throws \ReflectionException
	 */
	public function get( $key ) {
		// If the entry is already invoked we return it.
		if ( isset( $this->items[ $key ] ) ) {
			return $this->items[ $key ];
		}

		$definition = $this->getDefinition( $key );
		if ( ! $definition ) {
			throw new \RuntimeException( __( 'No entry or class found for ' . $key ) );
		}
		$this->items[ $key ] = $this->invokeDefinition( $definition );

		return $this->items[ $key ];
	}

	/**
	 * @inheritDoc
	 */
	public function set( $key, $definition ) {
		$this->definitions[ $key ] = $definition;
	}

	/**
	 * @param $key
	 *
	 * @return callable|false
	 */
	protected function getDefinition( $key ) {
		if ( isset( $this->definitions[ $key ] ) ) {
			return $this->definitions[ $key ];
		}
		return FALSE;
	}

	/**
	 * @param callable $definition
	 *
	 * @return mixed
	 * @throws \ReflectionException
	 */
	protected function invokeDefinition( $definition ) {
		return $this->invoker->call( $definition, [
			'container' => $this,
		] );
	}
}
