<?php

namespace Kinglet\Container;

use Kinglet\Invoker\Invoker;
use Kinglet\Invoker\InvokerInterface;

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
	 * @var InvokerInterface
	 */
	protected $invoker;

	public function __construct( array $definitions = [], InvokerInterface $invoker = NULL ) {
		$this->definitions = $definitions;
		$this->invoker = $invoker ? $invoker : $this->createInvoker();
	}

	/**
	 * Instantiate an invoker.
	 *
	 * @return InvokerInterface
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
	 */
	public function get( $key ) {
		// If the entry is already invoked we return it.
		if ( isset( $this->items[ $key ] ) ) {
			return $this->items[ $key ];
		}

		$definition = $this->getDefinition( $key );
		if ( !$definition ) {
			throw new \RuntimeException( __( 'No entry or class found for ' . $key ) );
		}
		try {
		    $this->items[ $key ] = $this->invokeDefinition( $definition );
		}
		catch( \ReflectionException $exception ) {
		    // Fail silently for now.
        }

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
     */
	protected function invokeDefinition( $definition ) {
        // ContainerInjectionInterface class
        if ( is_string( $definition ) && class_exists( $definition ) && is_a( $definition, 'Kinglet\Container\ContainerInjectionInterface', TRUE ) ) {
            $definition = [$definition, 'create'];
        }

        $instance = $this->invoker->call( $definition, [
            'container' => $this,
        ] );

        // ContainerAwareInterface class
        if ( is_a( $instance, 'Kinglet\Container\ContainerAwareInterface', TRUE ) ) {
            $instance->setContainer( $this );
        }

        return $instance;
	}
}
