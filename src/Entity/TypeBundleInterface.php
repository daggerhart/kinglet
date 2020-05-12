<?php

namespace Kinglet\Entity;

/**
 * Interface BundleInterface
 *
 * @package Kinglet\Entity
 */
interface TypeBundleInterface {

	/**
	 * Returns the bundle name/id.
	 *
	 * @return string
	 */
	public function bundle();

	/**
	 * Returns what information it can about the bundle.
	 *
	 * @return array
	 */
	public function bundleInfo();
}
