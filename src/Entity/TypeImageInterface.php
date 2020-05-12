<?php

namespace Kinglet\Entity;

/**
 * Interface ImageInterface
 *
 * @package Kinglet\Entity
 */
interface TypeImageInterface {

	/**
	 * HTML image for the object.
	 *
	 * @param null $size
	 *
	 * @return string
	 */
	public function image( $size = null );
}
