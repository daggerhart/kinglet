<?php

namespace Kinglet\Invoker;

use RuntimeException;

interface InvokerInterface {

	/**
	 * Call the given function using the given parameters.
	 *
	 * @param callable $callable Function to call.
	 * @param array $parameters Parameters to use.
	 *
	 * @return mixed Result of the function.
	 *
	 * @throws RuntimeException
	 */
	public function call( $callable, array $parameters = [] );

}
