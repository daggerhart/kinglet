<?php

namespace Kinglet\Admin;

use Kinglet\Container\ContainerInjectionInterface;
use Kinglet\Container\ContainerInterface;
use WP_User;

/**
 * Class MessengerUser. Queue for user specific messages.
 *
 * @package Kinglet\Admin
 */
class MessengerUser implements MessengerInterface, ContainerInjectionInterface {

	/**
	 * @var int
	 */
	protected $user_id;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var array
	 */
	protected $items = [];

	/**
	 * Messenger constructor.
	 *
	 * @param int|WP_User $user_id
	 * @param $name
	 */
	public function __construct( $user_id, $name = 'kinglet_admin_messages' ) {
		$this->user_id = ( $user_id instanceof WP_User ) ? $user_id->ID : $user_id;
		$this->setName( $name );
		$this->load();
	}

	/**
	 * {@inheritDoc}
	 */
	public static function create( ContainerInterface $container ) {
		return new static(
			$container->get( 'current_user' )
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function add( $message, $type ) {
		$this->items[ md5( $message . $type ) ] = [
			'message' => $message,
			'type' => $type,
			'timestamp' => time(),
		];
		$this->save();
	}

	/**
	 * {@inheritDoc}
	 */
	public function get() {
		if ( ! empty( $this->items ) ) {
			$messages = array_values( $this->items );
			$this->delete();

			return $messages;
		}

		return [];
	}

	/**
	 * Set the name of the meta store.
	 *
	 * @param string $meta_name
	 */
	public function setName( $meta_name ) {
		$this->name = $meta_name;
	}

	/**
	 * Load the queue.
	 */
	public function load() {
		$items = get_user_meta( $this->user_id, $this->name, true );
		if ( $items ) {
			$this->items = $items;
		}
	}

	/**
	 * Save items to queue.
	 */
	public function save() {
		update_user_meta( $this->user_id, $this->name, $this->items );
	}

	/**
	 * Delete the entire queue.
	 */
	public function delete() {
		delete_user_meta( $this->user_id, $this->name );
	}

}
