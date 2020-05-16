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
            $attribute = "{$prefix}{$name}";
			if ( is_array( $value ) ) {
				$value = implode( ' ', $value );
			}
			if ( $value !== TRUE ) {
                $value = esc_attr($value);
                $attribute.= "='{$value}'";
            }
			$attributes[] = $attribute;
		}
		return implode( ' ', $attributes );
	}

}
