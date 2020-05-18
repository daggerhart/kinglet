<?php

namespace Kinglet\Registry;

use ReflectionClass;

class ClassRegistry extends Registry implements ClassRegistryInterface {

	/**
	 * @inheritDoc
	 */
	public function getReflection( $key ) {
		$class_name = $this->get( $key );
		return new ReflectionClass( $class_name );
	}

	/**
	 * @inheritDoc
	 */
	public function getInstance( $key, array $parameters = [] ) {
		$reflection = $this->getReflection( $key );
		if ( null === $reflection->getConstructor() ) {
			return $reflection->newInstanceWithoutConstructor();
		}

		return $reflection->newInstanceArgs( $parameters );
	}

}
