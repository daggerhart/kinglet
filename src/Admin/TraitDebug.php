<?php

namespace Kinglet\Admin;

trait TraitDebug {

	/**
	 * Very simple debug.
	 *
	 * @param [...$v] Any number of values to debug.
	 *
	 * @return string
	 */
	public function d() {
		ob_start();
		foreach ( func_get_args() as $value ) {
			if ( function_exists( 'dump' ) ) {
				dump( $value );
			} else {
				echo "<pre>" . print_r( $value, 1 ) . "</pre>";
			}
		}
		echo ob_get_clean();
	}

}
