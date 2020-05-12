<?php

namespace Kinglet\Entity;

interface QueryInterface {

	/**
	 * Execute a query and return normalized results.
	 *
	 * @param null|callable $callback
	 *
	 * @return array
	 */
	public function execute( $callback = null );

}
