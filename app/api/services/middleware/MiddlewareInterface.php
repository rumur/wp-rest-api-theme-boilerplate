<?php

namespace App\Api\Service\Middleware;

use App\Api\Services\Http\Request;

/**
 * Interface MiddlewareInterface
 * @package App\Api\Service\Middleware
 */
Interface MiddlewareInterface {
    /**
     * Checks if a given request has access.
     *
     * @param Request  $request Full details about the request.
     * @see `wp-includes/rest-api.php:rest_send_allow_header`
     *
     * @return \WP_Error|bool True if the request has access, error object otherwise.
     *
     * @return mixed
     *
     * @author rumur
     */
    public function handle(Request $request);
}
