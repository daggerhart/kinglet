<?php

namespace Kinglet\Entity\Type;

use Kinglet\Entity\EntityBase;
use Kinglet\Entity\TypeAuthorInterface;
use Kinglet\Entity\TypeBundleInterface;
use Kinglet\Entity\TypeImageInterface;
use Kinglet\Entity\TypeTitleInterface;

/**
 * Class Post
 *
 * @package Kinglet\Entity\Type
 */
class Post extends EntityBase implements TypeBundleInterface, TypeTitleInterface, TypeImageInterface, TypeAuthorInterface {

	/**
	 * @var \WP_Post
	 */
	protected $object;

	/**
	 * {@inheritdoc}
	 */
	static public function load( $id ) {
		$object = get_post( $id );

		return new self( $object );
	}

	/**
	 * {@inheritdoc}
	 */
	public function type() {
		return 'post';
	}

	/**
	 * {@inheritdoc}
	 */
	public function id() {
		return $this->object->ID;
	}

	/**
	 * {@inheritdoc}
	 */
	public function content() {
		return apply_filters( 'the_content', get_post_field( 'post_content', $this->object) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function url() {
		return get_permalink( $this->object );
	}

	/**
	 * {@inheritdoc}
	 */
	public function slug() {
		return get_post_field( 'post_name', $this->object );
	}

	/**
	 * {@inheritdoc}
	 */
	public function title() {
		return get_the_title( $this->object );
	}

	/**
	 * {@inheritdoc}
	 */
	public function bundle() {
		return get_post_type( $this->object );
	}

	/**
	 * {@inheritdoc}
	 */
	public function bundleInfo() {
		return get_post_type_object( $this->bundle() );
	}

	/**
	 * {@inheritdoc}
	 */
	public function image( $size = null ) {
		return get_the_post_thumbnail( $this->object, $size );
	}

	/**
	 * {@inheritdoc}
	 */
	public function author() {
		$user = new \WP_User( $this->object->post_author );
		return new User( $user );
	}

}
