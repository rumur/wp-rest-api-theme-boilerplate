<?php

namespace App\Actions;

/**
 * Registers all available routes.
 *
 * @since v1.0.0
 */
add_action( 'rest_api_init', function () {
	$file = apply_filters( 'api_app_routes_file_path', "./app/api/routes.php" );
	locate_template( $file, true, true );
} );

