<?php

namespace App\Actions;

/**
 * Allow GET requests from * origin
 * Thanks to https://joshpress.net/access-control-headers-for-the-wordpress-rest-api/
 */
add_action( 'rest_api_init', function() {
    remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
    add_filter( 'rest_pre_serve_request', function( $value ) {
        header( 'Access-Control-Allow-Origin: *' );
        header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE' );
        header( 'Access-Control-Allow-Credentials: true' );
        return $value;
    });
}, 15 );
