<?php

namespace Kinglet\Admin;

use Kinglet\Container\ContainerInjectionInterface;
use Kinglet\Container\ContainerInterface;
use Kinglet\Entity\Type\User;
use Kinglet\Entity\TypeInterface;
use WP_User;

class Messenger implements ContainerInjectionInterface {

	/**
	 * @var TypeInterface
	 */
	protected $entity;

	/**
	 * @var string
	 */
	protected $metaName;

	/**
	 * @var array
	 */
	protected $items = [];

	/**
	 * Messenger constructor.
	 *
	 * @param int|WP_User $user_id
	 * @param $meta_name
	 */
	public function __construct( $user_id, $meta_name = 'kinglet_admin_messages' ) {
		$this->entity = User::load( $user_id );
		$this->setMetaName( $meta_name );
		$this->load();
	}

	public static function create( ContainerInterface $container ) {
		return new static(
			$container->get( 'current_user' )
		);
	}

	/**
	 * Set the name of the meta store.
	 *
	 * @param string $meta_name
	 */
	public function setMetaName( $meta_name ) {
		$this->metaName = $meta_name;
	}

	/**
	 * Load the meta store.
	 */
	public function load() {
		$items = $this->entity->meta( $this->metaName );
		if ( $items ) {
			$this->items = $items;
		}
	}

	/**
	 * Save the meta store.
	 */
	public function save() {
		$this->entity->metaUpdate( $this->metaName, $this->items );
	}

	/**
	 * Delete the entire meta store.
	 */
	public function delete() {
		$this->entity->metaDelete( $this->metaName );
	}

	/**
	 * Add a new item to the store.
	 *
	 * @param string $message
	 * @param string $type
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
	 * Get messages for the current user and clear them.
	 *
	 * @return array
	 */
	public function get() {
		if ( ! empty( $this->items ) ) {
			$messages = array_values( $this->items );
			$this->delete();

			return $messages;
		}

		return [];
	}

}
