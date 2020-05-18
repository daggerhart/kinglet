<?php

namespace Kinglet;

use Kinglet\Admin\Messenger;
use Kinglet\Container\Container;
use Kinglet\Container\ContainerInterface;
use Kinglet\FileSystem\Finder;
use Kinglet\Form\FormFactory;
use Kinglet\Invoker\Invoker;
use Kinglet\Registry\ClassRegistry;
use Kinglet\Registry\DiscoverableInterfaceRegistry;
use Kinglet\Template\CallableRenderer;
use Kinglet\Template\FileRenderer;
use Kinglet\Template\StringRenderer;

class Loader {

	const KINGLET_SRC = __DIR__;

	/**
	 * @return ContainerInterface
	 */
	public static function createContainer() {
		$container = new Container( [
			'invoker' => Invoker::class,
			'finder' => Finder::class,
			'renderer' => FileRenderer::class,
			'renderer.callable' => CallableRenderer::class,
			'renderer.string' => StringRenderer::class,
			'form.factory' => FormFactory::class,
			'current_user' => 'wp_get_current_user',
			'messenger' => Messenger::class,
		] );

		$container->set( 'entity.type.manager', function() {
			return new ClassRegistry( [
				'comment' => 'Kinglet\Entity\Type\Comment',
				'post' => 'Kinglet\Entity\Type\Post',
				'term' => 'Kinglet\Entity\Type\Term',
				'user' => 'Kinglet\Entity\Type\User',
			] );
		} );

		$container->set( 'entity.query.manager', function() {
			return new ClassRegistry( [
				'comment' => 'Kinglet\Entity\Query\Comments',
				'post' => 'Kinglet\Entity\Query\Posts',
				'term' => 'Kinglet\Entity\Query\Terms',
				'user' => 'Kinglet\Entity\Query\Users',
			] );
		} );

		$container->set( 'form.field_type.manager', function () {
			add_filter( 'kinglet--field-type--sources', function ( $sources ) {
				$sources['Kinglet\Form\Field'] = self::KINGLET_SRC . '/Form/Field';

				return $sources;
			} );

			return new DiscoverableInterfaceRegistry(
				'Kinglet\Form\FieldInterface',
				'type',
				'kinglet--field-type--sources'
			);
		} );

		$container->set( 'form.form_style.manager', function () {
			add_filter( 'kinglet--form-style--sources', function ( $sources ) {
				$sources['Kinglet\Form\Style'] = self::KINGLET_SRC . '/Form/Style';

				return $sources;
			} );

			return new DiscoverableInterfaceRegistry(
				'Kinglet\Form\FormStyleInterface',
				'type',
				'kinglet--form-style--sources'
			);
		} );

		return $container;
	}

}
