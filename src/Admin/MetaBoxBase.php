<?php

namespace Kinglet\Admin;

abstract class MetaBoxBase {

	use TraitDebug;

	/**
	 * @var string[]
	 */
	protected $screens = [];

	/**
	 * Whether or not this meta box has been registered.
	 *
	 * @var bool
	 */
	static protected $initialized = FALSE;

	/**
	 * MetaBoxBase constructor.
	 *
	 * @param string|string[] $screens
	 */
	public function __construct( $screens ) {
		$this->screens = (array) $screens;

		if ( !static::$initialized ) {
			$this->init();
		}
	}

	/**
	 * Unique ID for the meta box.
	 *
	 * @return string
	 */
	abstract public function id();

	/**
	 * Title for the meta box.
	 *
	 * @return string
	 */
	abstract public function title();

	/**
	 * Render the meta box HTML.
	 *
	 * @param $post
	 */
	abstract public function render( $post );

	/**
	 * Register the meta box with WordPress.
	 */
	protected function init() {
		static::$initialized = TRUE;
		add_action( 'add_meta_boxes', [ $this, 'addMetaBox' ] );

		foreach ( $this->screens as $screen ) {
			add_action( 'save_post_' . $screen, [ $this, 'save' ], 20, 3 );
		}
	}

	/**
	 * Add the meta box
	 */
	public function addMetaBox() {
		add_meta_box(
			$this->id(),
			$this->title(),
			[ $this, 'render' ],
			$this->screens
		);
	}

	/**
	 * Save post action.
	 *
	 * @param int $post_id
	 * @param \WP_Post $post
	 * @param bool $updated
	 */
	public function save( $post_id, $post, $updated ) {}

}
