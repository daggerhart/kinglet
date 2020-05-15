<?php

namespace Kinglet\Invoker;

use RuntimeException;
use ReflectionException;
use ReflectionFunctionAbstract;
use ReflectionFunction;
use ReflectionMethod;

/**
 * Class Invoker
 *
 * @package Kinglet\Invoker
 */
class Invoker implements InvokerInterface {

	/**
	 * @param $callable
	 * @param array $parameters
	 *
	 * @throws \ReflectionException
	 */
	public function call( $callable, array $parameters = [] ) {
		$reflection = $this->getReflection( $callable );
		$resolved_context = $this->resolveContext( $reflection, $parameters );
		call_user_func_array( $callable, $resolved_context );
	}

	/**
	 * Get the appropriate reflection for the callable.
	 *
	 * @link https://github.com/PHP-DI/Invoker/blob/master/src/Reflection/CallableReflection.php
	 *
	 * @param $callable
	 *
	 * @return ReflectionFunction|ReflectionMethod
	 * @throws \ReflectionException
	 */
	protected function getReflection( $callable ) {
		// Closure
		if ( $callable instanceof \Closure ) {
			return new ReflectionFunction( $callable );
		}

		// Array callable
		if ( is_array( $callable ) ) {
			list( $class, $method ) = $callable;

			if ( ! method_exists( $class, $method ) ) {
				throw new RuntimeException( __( "Method {$method} does not exist on class {$class}." ) );
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

		throw new RuntimeException( __( is_string( $callable ) ? $callable : 'Instance of ' . get_class( $callable ) . '%s is not a callable' ) );
	}

	/**
	 *
	 * @link https://github.com/PHP-DI/Invoker/blob/master/src/ParameterResolver/AssociativeArrayResolver.php
	 * @link https://github.com/PHP-DI/Invoker/blob/master/src/ParameterResolver/DefaultValueResolver.php
	 *
	 * @param \ReflectionFunctionAbstract $reflection
	 * @param $provided_parameters
	 *
	 * @return array
	 */
	protected function resolveContext( ReflectionFunctionAbstract $reflection, $provided_parameters ) {
		$reflection_parameters = $reflection->getParameters();
		$resolved_parameters = [];

		foreach ( $reflection_parameters as $index => $parameter ) {
			// Associative array parameters.
			if ( array_key_exists( $parameter->name, $provided_parameters ) ) {
				$resolved_parameters[ $index ] = $provided_parameters[ $parameter->name ];
			} // Optional named parameters.
			else if ( $parameter->isOptional() ) {
				try {
					$resolvedParameters[ $index ] = $parameter->getDefaultValue();
				} catch ( ReflectionException $e ) {
					// Can't get default values from PHP internal classes and functions
				}
			} // Typehinted parameters.
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
			/** @var \ReflectionParameter $parameter */
			$parameter = reset( $diff );
			$position = $parameter->getPosition() + 1;
			throw new RuntimeException( __( "Unable to invoke the callable because no value was given for parameter {$position} ({$parameter->name})" ) );
		}

		return $resolved_parameters;
	}

}
