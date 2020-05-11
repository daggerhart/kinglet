<?php

namespace Kinglet\Form;

trait TraitAttributes {

	/**
	 * Convert an array of key value pairs into HTML attributes.
	 *
	 * @param $array
	 * @param string $prefix
	 *
	 * @return string
	 */
	public function attributes( $array, $prefix ='' ) {
		$attributes = [];
		foreach ($array as $name => $value) {
			if ( is_array( $value ) ) {
				$value = implode( ' ', $value );
			}
			$value = esc_attr($value);
			$attributes[] = "{$prefix}{$name}='{$value}'";
		}
		return implode( ' ', $attributes );
	}

}
