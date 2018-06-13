<?php

namespace App\Api\Services\Http;

use \WP_Http as Status;
use \WP_Error as Driver;

/**
 * Class Response
 * @package App\Api\Services
 * @author  rumur
 */
class Response implements ResponseInterface {
    /** @var \WP_Error */
    protected $driver;

    /** @var int */
    protected $status = Status::OK;

    /**
     * Response constructor.
     */
    public function __construct()
    {
        $this->driver = new Driver();
    }

    /**
     * Factory.
     *
     * @param array $payload     See Response::add
     *
     * @return Response
     *
     * @author rumur
     */
    public static function make(array $payload)
    {
        $self = new self();

        $self->add( $payload );

        return $self;
    }

    /**
     * Prepare data for response.
     *
     * @param array $payload {
     *
     *     @type string       $handle          Response handle name.
     *     @type mixed        $data            Data which will be passed to the browser.
     *     @type int          $status          Server response status.
     *     @type string       $message         The Message for browser.
     * }
     * @return $this
     *
     * @author rumur
     */
    public function add(array $payload)
    {
        /**
         * @var array $data
         * @var string $handle
         * @var string $message
         */
        extract( array_merge( [
            'data'    => [],
            'handle'  => uniqid('api-'),
            'message' => 'Hello There!',
        ], $payload ) );

        // set status from $payload.
        isset( $status ) && $this->status = absint( $status );

        $this->driver->add( $handle, $message, $data );

        return $this;
    }

    /**
     * A Proxy to get all methods from the Driver.
     *
     * @param string $method
     * @param        $arguments
     *
     * @return mixed
     * @author rumur
     */
    public function __call($method, $arguments)
    {
        if ( is_callable( [ $this->driver, $method ], true ) ) {
            return call_user_func_array( [ $this->driver, $method ], $arguments );
        }
    }

    /**
     * @return mixed|\WP_REST_Response
     *
     * @author rumur
     */
    public function ok()
    {
        $this->status = Status::OK;

        return $this->dispatch();
    }

    /**
     * @return mixed|\WP_REST_Response
     *
     * @author rumur
     */
    public function notFound()
    {
        $this->status = Status::NOT_FOUND;

        return $this->dispatch();
    }

    /**
     * @return mixed|\WP_REST_Response
     *
     * @author rumur
     */
    public function forbidden()
    {
        $this->status = Status::FORBIDDEN;

        return $this->dispatch();
    }

    /**
     * @return mixed|\WP_REST_Response
     *
     * @author rumur
     */
    public function serverError()
    {
        $this->status = Status::INTERNAL_SERVER_ERROR;

        return $this->dispatch();
    }

    /**
     * Send back on Request.
     *
     * @see \WP_REST_Server::error_to_response
     *
     * @return mixed|\WP_REST_Response
     *
     * @author rumur
     */
    public function dispatch()
    {
        $errors = [];
        $driver = $this->driver;

        foreach ( (array) $driver->errors as $code => $messages ) {
            foreach ( (array) $messages as $message ) {
                $errors[] = [
                    'code' => $code,
                    'message' => $message,
                    'data' => $driver->get_error_data( $code )
                ];
            }
        }

        $data = $errors[0];

        if ( count( $errors ) > 1 ) {
            // Remove the primary error.
            array_shift( $errors );
            $data['additional_errors'] = $errors;
        }

        $response = new \WP_REST_Response( $data, $this->status );

        return $response;
    }

	/**
	 * Check if a given array is associative.
	 *
	 * @param array $arr
	 *
	 * @return bool True if associative.
	 */
	protected function isAssociative(array $arr)
	{
		if (empty($arr)) return false;
		return array_keys($arr) !== range(0, count($arr) - 1);
	}
}
