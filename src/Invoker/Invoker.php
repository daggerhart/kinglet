<?php

namespace Kinglet\Invoker;

use Closure;
use Kinglet\Container\ContainerInterface;
use Kinglet\Container\ContainerInjectionInterface;
use ReflectionParameter;
use ReflectionClass;
use ReflectionException;
use ReflectionFunctionAbstract;
use ReflectionFunction;
use ReflectionMethod;

/**
 * Class Invoker
 *
 * @package Kinglet\Invoker
 */
class Invoker implements InvokerInterface, ContainerInjectionInterface {

	/**
	 * @inheritDoc
	 */
	public static function create( ContainerInterface $container ) {
		return new static();
	}

	/**
	 * @param $callable
	 * @param array $parameters
	 *
	 * @return mixed
	 * @throws ReflectionException
	 * @throws NotEnoughParametersException
	 */
	public function call( $callable, array $parameters = [] ) {
		$reflection = $this->getReflection( $callable );
		if ( $reflection instanceof ReflectionClass ) {
			return $this->constructClass( $reflection, $parameters );
		}

		$resolved_parameters = $this->resolveParameters( $reflection, $parameters );
		return call_user_func_array( $callable, $resolved_parameters );
	}

	/**
	 * @param ReflectionClass $reflection
	 * @param array $parameters
	 *
	 * @return object
	 * @throws NotEnoughParametersException
	 */
	protected function constructClass( ReflectionClass $reflection, array $parameters ) {
		if ( null === $reflection->getConstructor() ) {
			return $reflection->newInstanceWithoutConstructor();
		}
		$resolved_parameters = $this->resolveParameters( $reflection->getConstructor(), $parameters );

		return $reflection->newInstanceArgs( $resolved_parameters );
	}

	/**
	 * Get the appropriate reflection for the callable.
	 *
	 * @link https://github.com/PHP-DI/Invoker/blob/master/src/Reflection/CallableReflection.php
	 *
	 * @param $callable
	 *
	 * @return ReflectionFunction|ReflectionMethod|ReflectionClass
	 * @throws InvokerReflectionException
	 * @throws ReflectionException
	 */
	protected function getReflection( $callable ) {
		// Closure
		if ( $callable instanceof Closure ) {
			return new ReflectionFunction( $callable );
		}

		// Array callable
		if ( is_array( $callable ) ) {
			list( $class, $method ) = $callable;

			if ( ! method_exists( $class, $method ) ) {
				throw new InvokerReflectionException( __( "Method {$method} does not exist on class {$class}." ) );
			}

			return new ReflectionMethod( $class, $method );
		}

		// Callable object (i.e. implementing __invoke())
		if ( is_object( $callable ) && method_exists( $callable, '__invoke' ) ) {
			return new ReflectionMethod( $callable, '__invoke' );
		}

		// Callable class (i.e. implementing __invoke())
		if ( is_string( $callable ) && class_exists( $callable ) && method_exists( $callable, '__invoke' ) ) {
			return new ReflectionMethod( $callable, '__invoke' );
		}

		// Standard function
		if ( is_string( $callable ) && function_exists( $callable ) ) {
			return new ReflectionFunction( $callable );
		}

		// Standard class
		if ( is_string( $callable ) && class_exists( $callable ) ) {
			$class = new ReflectionClass( $callable );
			if ( $class->isInstantiable() ) {
				return $class;
			}
		}

		throw new InvokerReflectionException( __( is_string( $callable ) ? $callable : 'Instance of ' . get_class( $callable ) . '%s is not a callable' ) );
	}

	/**
	 * Match the provided parameters to the reflection signature.
	 *
	 * @link https://github.com/PHP-DI/Invoker/blob/master/src/ParameterResolver/NumericArrayResolver.php
	 * @link https://github.com/PHP-DI/Invoker/blob/master/src/ParameterResolver/AssociativeArrayResolver.php
	 * @link https://github.com/PHP-DI/Invoker/blob/master/src/ParameterResolver/DefaultValueResolver.php
	 *
	 * @param ReflectionFunctionAbstract $reflection
	 * @param $provided_parameters
	 *
	 * @return array
	 * @throws NotEnoughParametersException
	 */
	protected function resolveParameters( ReflectionFunctionAbstract $reflection, $provided_parameters ) {
		$reflection_parameters = $reflection->getParameters();
		$resolved_parameters = [];

		// Numeric array parameters.
		foreach ( $provided_parameters as $index => $value ) {
			if ( is_int( $index ) ) {
				$resolved_parameters[ $index ] = $value;
			}
		}

		foreach ( $reflection_parameters as $index => $parameter ) {
			if ( isset( $resolved_parameters[ $index ] ) ) {
				continue;
			}

			// Associative array parameters.
			if ( array_key_exists( $parameter->name, $provided_parameters ) ) {
				$resolved_parameters[ $index ] = $provided_parameters[ $parameter->name ];
			}
			// Optional named parameters.
			else if ( $parameter->isOptional() ) {
				try {
					$resolved_parameters[ $index ] = $parameter->getDefaultValue();
				}
				catch ( ReflectionException $e ) {
					// Can't get default values from PHP internal classes and functions
				}
			}
			// Typehinted parameters.
			else {
				$parameter_class = $parameter->getClass();
				if ( $parameter_class && array_key_exists( $parameter_class->name, $provided_parameters ) ) {
					$resolved_parameters[ $index ] = $provided_parameters[ $parameter_class->name ];
				}
			}
		}

		// Check all parameters are resolved
		$diff = array_diff_key( $reflection->getParameters(), $resolved_parameters );
		if ( ! empty( $diff ) ) {
			/** @var ReflectionParameter $parameter */
			$parameter = reset( $diff );
			$position = $parameter->getPosition() + 1;
			throw new NotEnoughParametersException( __( "Unable to invoke the callable {$reflection->getName()} because no value was given for parameter {$position} ({$parameter->name})" ) );
		}

		return $resolved_parameters;
	}

}
