<?php

namespace Kinglet;

use Kinglet\Admin\Messenger;
use Kinglet\Container\Container;
use Kinglet\Container\ContainerInterface;
use Kinglet\FileSystem\Finder;
use Kinglet\Form\FormFactory;
use Kinglet\Invoker\Invoker;
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

		// Field Types
		$container->set( 'form.field_types', function () {
			add_filter( 'kinglet--field-types--sources', function ( $sources ) {
				$sources['Kinglet\Form\Field'] = self::KINGLET_SRC . '/Form/Field';

				return $sources;
			} );

			return new DiscoverableInterfaceRegistry(
				'Kinglet\Form\FieldInterface',
				'name',
				'kinglet--field-types--sources'
			);
		} );

		// Form Styles
		$container->set( 'form.form_styles', function () {
			add_filter( 'kinglet--form-styles--sources', function ( $sources ) {
				$sources['Kinglet\Form\Style'] = self::KINGLET_SRC . '/Form/Style';

				return $sources;
			} );

			return new DiscoverableInterfaceRegistry(
				'Kinglet\Form\FormStyleInterface',
				'name',
				'kinglet--form-styles--sources'
			);
		} );

		return $container;
	}

}
