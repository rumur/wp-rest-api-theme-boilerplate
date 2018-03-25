<?php

namespace App\Service\Middleware;

use \WP_REST_Request as Request;

/**
 * Interface MiddlewareInterface
 */
Interface MiddlewareInterface {
	/**
	 * Checks if a given request has access.
	 *
	 * @param  \WP_REST_Request  $request Full details about the request.
	 *
	 * @return \WP_Error|bool True if the request has access, error object otherwise.
	 */
	public function handle( Request $request );
}
