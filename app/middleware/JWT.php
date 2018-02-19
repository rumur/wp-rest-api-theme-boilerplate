<?php

namespace App\Middleware;

use \WP_REST_Request as Request;
use App\Services\Http\RequestAdapter;
use App\Middleware\Contract\MiddlewareInterface;

/**
 * Class JWT
 * @package App\Middleware
 * @author  rumur
 */
class JWT implements MiddlewareInterface {
	/**
	 * Checks if a given request has access.
	 *
	 * @param  \WP_REST_Request  $request Full details about the request.
	 *
	 * @return \WP_Error|bool    True if the request has access, error object otherwise.
	 */
	public function handle( Request $request )
	{
		/** @var \App\Services\Http\RequestAdapter */
		$r = RequestAdapter::make( $request );

		$token = $r->getTokenFromRequest();

		if ( ! is_wp_error( $token ) ) {
			$token = $r->validateToken();
		}

		return ! is_wp_error( $token )
			? true
			: $token;
	}
}
