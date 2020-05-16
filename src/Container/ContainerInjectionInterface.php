<?php

namespace Kinglet\Container;

interface ContainerInjectionInterface {

	/**
	 * @param ContainerInterface $container
	 *
	 * @return static
	 */
	public static function create( ContainerInterface $container );

}
