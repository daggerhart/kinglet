<?php

namespace Kinglet\Entity\Type;

use Kinglet\Entity\EntityBase;
use Kinglet\Entity\TypeAuthorInterface;
use Kinglet\Entity\TypeImageInterface;
use Kinglet\Entity\TypeTitleInterface;

/**
 * Class Comment
 *
 * @package Kinglet\Entity\Type
 */
class Comment extends EntityBase implements TypeTitleInterface, TypeImageInterface,TypeAuthorInterface {

	/**
	 * @var \WP_Comment
	 */
	protected $object;

	/**
	 * {@inheritdoc}
	 */
	static public function load( $id ) {
		$object = get_comment( $id );

		return new self( $object );
	}

	/**
	 * {@inheritdoc}
	 */
	public function type() {
		return 'comment';
	}

	/**
	 * {@inheritdoc}
	 */
	public function id() {
		return $this->object->comment_ID;
	}

	/**
	 * Comments have a fake slug based on a fake title.
	 *
	 * {@inheritdoc}
	 */
	public function slug() {
		return sanitize_title( $this->title() );
	}

	/**
	 * {@inheritdoc}
	 */
	public function content() {
		return get_comment_text( $this->object );
	}

	/**
	 * {@inheritdoc}
	 */
	public function url() {
		return get_comment_link( $this->object );
	}

	/**
	 * Comments don't have titles, but they should. Fake it.
	 *
	 * {@inheritdoc}
	 */
	public function title() {
		$clean = strip_tags( $this->content() );
		return wp_trim_words( $clean, 8, '' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function image( $size = NULL ) {
		return get_avatar( $this->author()->email(), $size, '', $this->author()->title() );
	}

	/**
	 * {@inheritdoc}
	 */
	public function author() {
		if ( $this->object->user_id ) {
			$user = new \WP_User( $this->object->user_id );
		}
		else {
			$data = new \stdClass();
			$data->ID = 0;
			$data->display_name = $this->object->comment_author;
			$data->user_nicename = sanitize_title_with_dashes( $this->object->comment_author );
			$data->user_email = $this->object->comment_author_email;
			$data->user_url = $this->object->comment_author_url;

			$user = new \WP_User( $data );
		}

		return new User( $user );
	}

}
