<?php

namespace App\Actions;

/**
 * Registers all available routes.
 *
 * @since v1.0.0
 */
add_action( 'rest_api_init', function () {
	$file = "./app/routes.php";
	locate_template( $file, true, true );
} );

