<?php

namespace App\Api\Services\Http;

/**
 * Interface ResponseInterface
 * @package App\Api\Services\Http
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
	public static function make(array $payload);

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
	 * @return mixed|\WP_REST_Response
	 *
	 * @author rumur
	 */
	public function dispatch();
}
