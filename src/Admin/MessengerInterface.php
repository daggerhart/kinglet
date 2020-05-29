<?php

namespace Kinglet\Admin;

interface MessengerInterface {

	/**
	 * Add a new item to the message queue.
	 *
	 * @param string $message
	 * @param string $type Either "updated" or "error".
	 */
	public function add( $message, $type );

	/**
	 * Get all queued messages.
	 *
	 * @return array
	 */
	public function get();

}
