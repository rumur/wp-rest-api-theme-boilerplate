<?php

namespace App\Services\Http;

/**
 * Interface ResponseInterface
 * @package App\Services\Http
 */
interface ResponseInterface {
	/**
	 * Factory.
	 *
	 * @param array $payload     See Response::add
	 *
	 * @return Response
	 *
	 * @author rumur
	 */
	public static function make( array $payload );

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
	public function add(array $payload);

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
	public function dispatch();
}