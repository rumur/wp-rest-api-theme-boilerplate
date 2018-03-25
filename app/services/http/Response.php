<?php

namespace App\Services\Http;

use \WP_Http, WP_Error as Driver;

/**
 * Class Response
 * @package App\Services
 * @author  rumur
 */
class Response implements ResponseInterface {
	/** @var \WP_Error */
	protected $driver;

	/** @var string  */
	protected $status = WP_Http::OK;

	/**
	 * Response constructor.
	 */
	public function __construct()
	{
		$this->driver = new Driver();
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
	public function __call( $method, $arguments )
	{
		if ( is_callable( [ $this->driver, $method ], true ) ) {
			return call_user_func_array( [ $this->driver, $method ], $arguments );
		}
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
	public static function make( array $payload )
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
		extract( array_merge( [
			'data'    => [],
			'handle'  => uniqid('api-'),
			'status'  => $this->status,
			'message' => 'test',
		], $payload ) );

		$this->driver->add( $handle, $message, $data );

		return $this;
	}

	/**
	 * Shows the User Browser.
	 *
	 *
	 * @see \WP_REST_Server::error_to_response
	 *
	 * @return mixed|\WP_Error
	 *
	 * @author rumur
	 */
	public function dispatch()
	{
		return $this->driver;
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
