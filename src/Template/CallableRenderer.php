<?php

namespace Kinglet\Template;

use Kinglet\Invoker\Invoker;
use Kinglet\Invoker\InvokerInterface;
use RuntimeException;
use ReflectionException;

class CallableRenderer extends RendererBase {

	/**
	 * Renderer configuration options.
	 *
	 * @var array
	 */
	protected $options = [
		'silent' => TRUE,
	];


	protected $invoker;

	/**
	 * CallableRenderer constructor.
	 *
	 * @param array $options
	 * @param \Kinglet\Invoker\InvokerInterface|NULL $invoker
	 */
	public function __construct( $options = [], InvokerInterface $invoker = null ) {
		$this->invoker = $invoker ? $invoker : $this->createInvoker();
		parent::__construct( $options );
	}

	/**
	 * Simple invoker.
	 *
	 * @return \Kinglet\Invoker\Invoker
	 */
	protected function createInvoker() {
		return new Invoker();
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
			$this->invoker->call( $template, $context );
			return ob_get_clean();
		} catch ( ReflectionException $exception ) {
			if ( $this->options['silent'] ) {
				return "<!-- {$exception->getMessage()} -->";
			}
			throw new RuntimeException( $exception->getMessage() );
		}
	}

}
