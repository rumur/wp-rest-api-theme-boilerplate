<?php

namespace App\Middleware;

use \WP_REST_Request as Request;
use App\Service\Middleware\Middleware;

/**
 * Class JWTMiddleware
 * @package App\Middleware
 * @author  rumur
 */
class LoggedInMiddleware extends Middleware {
    /**
     * Checks if a given request has access.
     *
     * @param  \WP_REST_Request  $request Full details about the request.
     *
     * @return bool    True if the request has access.
     */
    public function handle( Request $request )
    {
        return is_user_logged_in();
    }
}
