<?php

namespace Kinglet\Container;

interface ContainerInterface extends \Psr\Container\ContainerInterface {

	/**
	 * Set a specific item in the container.
	 *
	 * @param string $id
	 *   The name of the definition.
	 * @param callable $value
	 *   The new definition.
	 *
	 * @void
	 */
	public function set( $id, $value );

}
