<?php

namespace Kinglet\Entity\Type;

use Kinglet\Entity\TypeBase;
use Kinglet\Entity\TypeAuthorInterface;
use Kinglet\Entity\TypeBundleInterface;
use Kinglet\Entity\TypeImageInterface;
use Kinglet\Entity\TypeTitleInterface;
use WP_User;

/**
 * Class User
 *
 * @package Kinglet\Entity\Type
 */
class User extends TypeBase implements TypeBundleInterface, TypeTitleInterface, TypeImageInterface, TypeAuthorInterface {

	/**
	 * @var WP_User
	 */
	protected $object;

	/**
	 * {@inheritdoc}
	 */
	static public function load( $id ) {
		if ( $id instanceof WP_User ) {
			return new static ( $id );
		}

		$object = get_user_by( 'id', $id );

		return new static( $object );
	}

	/**
	 * {@inheritdoc}
	 */
	public function type() {
		return 'user';
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
		return $this->meta( 'description' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function url() {
		return get_author_posts_url( $this->id() );
	}

	/**
	 * {@inheritdoc}
	 */
	public function slug() {
		return $this->object->user_nicename;
	}

	/**
	 * {@inheritdoc}
	 */
	public function title() {
		return $this->object->display_name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function bundle() {
		return $this->object->roles;
	}

	/**
	 * Roles can act as bundle information. The alternative is that Users don't
	 * have bundles.
	 *
	 * {@inheritdoc}
	 */
	public function bundleInfo() {
		$roles = [];

		foreach ( $this->object->roles as $role ) {
			$roles[ $role ] = get_role( $role );
		}

		return $roles;
	}

	/**
	 * {@inheritdoc}
	 */
	public function image( $size = null ) {
		return get_avatar( $this->id(), $size, '', $this->title() );
	}

	/**
	 * {@inheritdoc}
	 */
	public function author() {
		return $this;
	}

	/**
	 * The User's email address.
	 *
	 * @return string
	 */
	public function email() {
		return $this->meta( 'user_email' );
	}

}
