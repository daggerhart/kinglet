<?php

namespace Kinglet\Entity\Type;

use Kinglet\Entity\EntityBase;
use Kinglet\Entity\TypeBundleInterface;
use Kinglet\Entity\TypeTitleInterface;

/**
 * Class Term
 *
 * @package Kinglet\Entity\Type
 */
class Term extends EntityBase implements TypeBundleInterface, TypeTitleInterface {

	/**
	 * @var \WP_Term
	 */
	protected $object;

	/**
	 * {@inheritdoc}
	 */
	static public function load( $id ) {
		global $wpdb;

		$taxonomy = $wpdb->get_var( $wpdb->prepare( "SELECT `taxonomy` FROM {$wpdb->term_taxonomy} WHERE `term_id` = %d LIMIT 1", $id ) );
		$object = get_term( $id, $taxonomy );

		return new self( $object );
	}

	/**
	 * {@inheritdoc}
	 */
	public function type() {
		return 'term';
	}

	/**
	 * {@inheritdoc}
	 */
	public function id() {
		return $this->object->term_id;
	}

	/**
	 * {@inheritdoc}
	 */
	public function slug() {
		return $this->object->slug;
	}

	/**
	 * {@inheritdoc}
	 */
	public function content() {
		return $this->object->description;
	}

	/**
	 * {@inheritdoc}
	 */
	public function url() {
		return get_term_link( $this->object, $this->bundle() );
	}

	/**
	 * {@inheritdoc}
	 */
	public function title() {
		return $this->object->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function bundle() {
		return $this->object->taxonomy;
	}

	/**
	 * {@inheritdoc}
	 */
	public function bundleInfo() {
		return get_taxonomy( $this->bundle() );
	}

}
