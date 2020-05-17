<?php

namespace Kinglet\Form;

use Kinglet\Container\ContainerInterface;
use Kinglet\Container\ContainerAwareInterface;

class FormFactory implements ContainerAwareInterface {

	/**
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * @param ContainerInterface $container
	 */
	public function setContainer( ContainerInterface $container ) {
		$this->container = $container;
	}

	/**
	 * Create a new Form instance.
	 *
	 * @param array $form_options
	 *
	 * @return Form
	 */
	public function create( $form_options = [] ) {
		return new Form(
			$form_options,
			$this->container->get( 'renderer.callable' ),
			$this->container->get( 'form.form_style.manager' ),
			$this->container->get( 'form.field_type.manager' )
		);
	}

}
