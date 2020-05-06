<?php

namespace Kinglet;

interface TaxonomyInterface {

	/**
	 * @return string
	 */
	static public function slug();

	/**
	 * Entry point.
	 *
	 * @return void
	 */
	static public function bootstrap();

	/**
	 * Register the taxonomy.
	 *
	 * @link https://codex.wordpress.org/Function_Reference/register_taxonomy
	 * @void
	 */
	public function register();

}
