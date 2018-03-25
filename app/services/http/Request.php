<?php

namespace App\Services\Http;

/**
 * Class Request
 * @package App\Services\Http
 * @author  rumur
 */
class Request extends \WP_REST_Request {
	/**
	 * Request constructor.
	 *
	 * @param string $method
	 * @param string $route
	 * @param array  $attributes
	 * @param bool   $load
	 */
	public function __construct( $method = '', $route = '', $attributes = array(), $load = true )
	{
		parent::__construct( $method, $route, $attributes );

		if ( $load ) {
			$this->load();
		}
	}

	/**
	 * Loads request.
	 *
	 * @author rumur
	 */
	public function load()
	{
		$server = new Server();

		$this->set_query_params( wp_unslash( $_GET ) );
		$this->set_body_params( wp_unslash( $_POST ) );
		$this->set_file_params( $_FILES );
		$this->set_headers( $server->get_headers( wp_unslash( $_SERVER ) ) );
		$this->set_body( $server->get_raw_data() );

		return $this;
	}

	/**
	 * Factory.
	 *
	 * @return Request
	 *
	 * @author rumur
	 */
	public static function make()
	{
		$request = new self( $_SERVER['REQUEST_METHOD'], '/' );

		return $request->load();
	}
}
