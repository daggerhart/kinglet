<?php

namespace Kinglet\Template;

use RuntimeException;
use ReflectionException;
use ReflectionFunctionAbstract;
use ReflectionFunction;
use ReflectionMethod;

class CallableRenderer extends RendererBase {

	/**
	 * Renderer configuration options.
	 *
	 * @var array
	 */
	protected $options = [
		'silent' => TRUE,
	];

	/**
	 * Render a callback as if it were a template. Entire context is pass in as
	 * single array.
	 *
	 * @link https://github.com/PHP-DI/Invoker/blob/master/src/Invoker.php
	 *
	 * @param callable $template
	 *   Function, method, or other callable that acts as the template.
	 * @param array $context
	 *   Context to be expanded as
	 *
	 * @return string
	 */
	public function render( $template, $context = [] ) {
		if ( ! is_callable( $template ) ) {
			throw new RuntimeException( __( 'Template is not callable.' ) );
		}

		try {
			$reflection = $this->getReflection( $template );
			$resolved_context = $this->resolveContext( $reflection, $context );
			ob_start();
			call_user_func_array( $template, $resolved_context );
			return ob_get_clean();
		} catch ( ReflectionException $exception ) {
			if ( $this->options['silent'] ) {
				return "<!-- {$exception->getMessage()} -->";
			}
			throw new RuntimeException( $exception->getMessage() );
		}
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
			if ( array_key_exists( $parameter->name, $provided_parameters ) ) {
				$resolved_parameters[ $index ] = $provided_parameters[ $parameter->name ];
			} else if ( $parameter->isOptional() ) {
				try {
					$resolvedParameters[ $index ] = $parameter->getDefaultValue();
				} catch ( ReflectionException $e ) {
					// Can't get default values from PHP internal classes and functions
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

}
