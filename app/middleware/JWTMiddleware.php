<?php

namespace App\Middleware;

use \WP_REST_Request as Request;
use App\Services\Http\RequestJWTAdapter;

/**
 * Class JWTMiddleware
 * @package App\Middleware
 * @author  rumur
 */
class JWTMiddleware extends Middleware {
	/**
	 * Checks if a given request has access.
	 *
	 * @param  \WP_REST_Request  $request Full details about the request.
	 *
	 * @return \WP_Error|bool    True if the request has access, error object otherwise.
	 */
	public function handle( Request $request )
	{
		/** @var \App\Services\Http\RequestJWTAdapter */
		$request = RequestJWTAdapter::make( $request );

		$token = $request->getTokenFromRequest();

		return is_wp_error( $token ) ? $token : $request->validateToken();
	}
}
