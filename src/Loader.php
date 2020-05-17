<?php

namespace Kinglet;

use Kinglet\Admin\Messenger;
use Kinglet\Container\Container;
use Kinglet\Container\ContainerInterface;
use Kinglet\FileSystem\Finder;
use Kinglet\Form\FormFactory;
use Kinglet\Invoker\Invoker;
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

		$container->set( 'entity.type.manager', function() {
			add_filter( 'kinglet--entity-type--manager', function ( $sources ) {
				$sources['\Kinglet\Entity\Type'] = self::KINGLET_SRC . '/Entity/Type';
				return $sources;
			} );

			return new DiscoverableInterfaceRegistry(
				'Kinglet\Entity\TypeInterface',
				'type',
				'kinglet--entity-type--manager'
			);
		} );

		$container->set( 'entity.query.manager', function() {
			add_filter( 'kinglet--entity-query--manager', function ( $sources ) {
				$sources['\Kinglet\Entity\Query'] = self::KINGLET_SRC . '/Entity/Query';
				return $sources;
			} );

			return new DiscoverableInterfaceRegistry(
				'Kinglet\Entity\QueryInterface',
				'type',
				'kinglet--entity-query--manager'
			);
		} );

		return $container;
	}

}
