<?php

namespace Kinglet;

interface PostTypeInterface {

	/**
	 * Entry point. Add WordPress hooks.
	 *
	 * @return void
	 */
	static public function bootstrap();

	/**
	 * Post type slug.
	 *
	 * @return string
	 */
	static public function slug();

	/**
	 * Register the post type.
	 * @link https://codex.wordpress.org/Function_Reference/register_post_type
	 * @void
	 */
	public function register();

}
