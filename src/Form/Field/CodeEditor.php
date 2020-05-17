<?php

namespace Kinglet\Form\Field;

class CodeEditor extends Textarea {

	/**
	 * @inheritDoc
	 */
	public function type() {
		return 'code_editor';
	}

	/**
	 * @link https://make.wordpress.org/core/2017/10/22/code-editing-improvements-in-wordpress-4-9/
	 *
	 * @inheritDoc
	 */
	public function render( $field ) {
		parent::render( $field );
		$field['editor_settings'] = $field['editor_settings'] ?? [];
		$editor_settings = [
			'type' => 'text/html',
			'codemirror' => [
				'indentUnit' => 2,
				'tabSize' => 2,
				'lint' => false,
			]
		];
		$editor_settings = array_replace( $editor_settings, $field['editor_settings'] );
		$settings = wp_enqueue_code_editor( $editor_settings );
		if ( ! $settings ) {
			return;
		}
		$init_script = sprintf(
			'wp.codeEditor.initialize( document.getElementById("%s"), %s );',
			$field['id'],
			wp_json_encode( $settings )
		);
		wp_add_inline_script( 'code-editor', $init_script, 'after' );
	}
}
