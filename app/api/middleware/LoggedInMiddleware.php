<?php

namespace App\Api\Middleware;

use App\Api\Services\Http\Request;
use App\Api\Service\Middleware\Middleware;

/**
 * Class LoggedInMiddleware
 * @package App\Api\Middleware
 * @author  rumur
 */
class LoggedInMiddleware extends Middleware {
    /**
     * Checks if a given request has access.
     *
     * @param  Request  $request Full details about the request.
     *
     * @return bool    True if the request has access.
     */
    public function handle( Request $request )
    {
        return is_user_logged_in();
    }
}
