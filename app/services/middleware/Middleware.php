<?php

namespace App\Service\Middleware;

use App\Services\Http\Request;

/**
 * Class Middleware
 * @package App\Middleware
 * @author  rumur
 */
abstract class Middleware implements MiddlewareInterface {
    /** @var Request */
    protected $request;

    /**
     * Middleware constructor.
     */
    public function __construct()
    {
        $this->boot();
    }

    /**
     * Boots all necessary stuff.
     *
     * @author rumur
     */
    public function boot()
    {
        $this->request = \App\app( 'request' );
    }
}
