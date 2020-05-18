<?php

namespace Kinglet\Registry;

use ReflectionClass;
use ReflectionException;

interface ClassRegistryInterface extends RegistryInterface {

	/**
	 * @param string $key
	 *
	 * @return ReflectionClass
	 * @throws ReflectionException
	 */
	public function getReflection( $key );

	/**
	 * @param string $key
	 * @param array $parameters
	 *
	 * @return object
	 * @throws ReflectionException
	 */
	public function getInstance( $key, array $parameters = [] );

}
