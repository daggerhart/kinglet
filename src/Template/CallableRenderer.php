<?php

namespace Kinglet\Template;

use Kinglet\Container\ContainerInterface;
use Kinglet\Container\ContainerInjectionInterface;
use Kinglet\Invoker\InvokerInterface;
use RuntimeException;
use ReflectionException;

class CallableRenderer extends RendererBase implements ContainerInjectionInterface {

	/**
	 * Renderer configuration options.
	 *
	 * @var array
	 */
	protected $default_options = [
		'silent' => true,
	];

	/**
	 * @var InvokerInterface
	 */
	protected $invoker;

	/**
	 * CallableRenderer constructor.
	 *
	 * @param array $options
	 */
	public function __construct( $options = [] ) {
		parent::__construct( $options );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function create( ContainerInterface $container ) {
		$static = new static();
		$static->setInvoker( $container->get( 'invoker' ) );

		return $static;
	}

	/**
	 * @param InvokerInterface $invoker
	 */
	public function setInvoker( InvokerInterface $invoker ) {
		$this->invoker = $invoker;
	}

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
			ob_start();
			echo $this->invoker->call( $template, $context );

			return ob_get_clean();
		}
		catch ( ReflectionException $exception ) {
			if ( $this->options['silent'] ) {
				return "<!-- {$exception->getMessage()} -->";
			}
			throw new RuntimeException( $exception->getMessage() );
		}
	}

}
