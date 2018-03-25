<?php

namespace App\Services\Http;

use \WP_Http;
use Firebase\JWT\JWT;

/**
 * Class RequestJWTTrait
 * @package App\Services\Http
 * @author  rumur
 */
trait RequestJWTTrait {
	/** @var string */
	private $token;
	/** @var string */
	private $secret_key; // defined('JWT_AUTH_SECRET_KEY') ? JWT_AUTH_SECRET_KEY : false;
	/** @var Response  */
	protected $response;

	/**
	 * Gets token from the HTTP Headers.
	 * @return \WP_Error| String
	 *
	 * @author rumur
	 */
	public function getTokenFromRequest()
	{
		if ( $auth = $this->get_header( 'Authorization' ) ) {
			list( $token ) = sscanf( $auth, 'Bearer %s' );

			return $token
				? $token
				: $this->response->add( [
						'status'  => WP_Http::FORBIDDEN,
						'handle'  => 'api_no_auth_token',
						'data'    => [],
						'message' => __( 'Authorization token is missed.', 'api' ),
					] )->dispatch();
		}

		return $this->response->add( [
			'status'  => WP_Http::FORBIDDEN,
			'handle'  => 'api_no_auth_header',
			'data'    => [],
			'message' => __( 'Authorization header not found.', 'api' ),
		] )->dispatch();
	}

	/**
	 * Set the given Token
	 *
	 * @param $token
	 *
	 * @author rumur
	 */
	protected function setToken($token)
	{
		$this->token = $token;
	}

	/**
	 * Gets the token.
	 *
	 * @return string
	 *
	 * @author rumur
	 */
	public function getToken()
	{
		return $this->token;
	}

	/**
	 * Gets the secret keys.
	 *
	 * @return bool|mixed|string
	 *
	 * @author rumur
	 */
	protected function getSecretKey()
	{
		return $this->secret_key;
	}

	/**
	 * @return object
	 *
	 * @author rumur
	 */
	public function getDecodedToken()
	{
		$secret_key = $this->getSecretKey();

		$token = JWT::decode( $this->getToken(), $secret_key, [ 'HS256' ] );

		return $token['data'];
	}

	/**
	 * @return array|\WP_Error
	 *
	 * @author rumur
	 */
	public function generateTokenData()
	{
		$username = $this->get_param('username');
		$password = $this->get_param('password');

		$secret_key = $this->getSecretKey();

		/** First thing, check the secret key if not exist return a error*/
		if ( ! $secret_key ) {
			return $this->response->add( [
				'status'  => WP_Http::FORBIDDEN,
				'handle'  => 'jwt_auth_bad_config',
				'data'    => [],
				'message' => __( 'JWT is not configured properly, please contact the admin.', 'api' ),
			] )->dispatch();
		}

		/** Try to authenticate the user with the passed credentials*/
		$user = wp_authenticate( $username, $password );

		/** If the authentication fails return a error*/
		if ( is_wp_error( $user ) ) {
			$error_code = $user->get_error_code();

			return $this->response->add( [
				'status'  => WP_Http::FORBIDDEN,
				'handle'  => "jwt_auth_{$error_code}",
				'data'    => [],
				'message' => $user->get_error_message( $error_code ),
			] )->dispatch();
		}

		/** Valid credentials, the user exists create the according Token */
		$issuedAt = time();
		$notBefore = $issuedAt;
		$expire = $issuedAt + ( DAY_IN_SECONDS * 1 );

		$token = [
			'iss'  => get_bloginfo( 'url' ),
			'iat'  => $issuedAt,
			'nbf'  => $notBefore,
			'exp'  => $expire,
			'data' => [
				'user' => [
					'id' => $user->data->ID,
				],
			],
		];

		/** Let the user modify the token data before the sign. */
		$token = JWT::encode( $token, $secret_key );

		/** The token is signed, now create the object with no sensible user data to the client*/
		$data = [
			'token' => $token,
			'email' => $user->data->user_email,
			'username' => $user->data->display_name,
			'nicename' => $user->data->user_nicename,
		];

		$this->setToken( $token );

		return $data;
	}

	/**
	 * Validate the given token.
	 *
	 * @return \WP_Error|true
	 *
	 * @author rumur
	 */
	public function validateToken()
	{
		$token = $this->getToken();

		if ( ! $token ) {
			return $this->response->add( [
				'status'  => WP_Http::FORBIDDEN,
				'handle'  => "api_no_auth_token",
				'data'    => [],
				'message' => __( 'Authorization token is missed.', 'api' ),
			] )->dispatch();
		}

		/** Get the Secret Key */
		$secret_key = $this->getSecretKey();

		if ( ! $secret_key ) {
			return $this->response->add( [
				'status'  => WP_Http::FORBIDDEN,
				'handle'  => "jwt_auth_bad_config",
				'data'    => [],
				'message' => __( 'JWT is not configured properly, please contact the admin.', 'api' ),
			] )->dispatch();
		}

		/** Try to decode the token */
		try {
			$token = $this->getDecodedToken();

			/** The Token is decoded now validate the iss */
			if ( $token->iss != get_bloginfo( 'url' ) ) {
				/** The iss do not match, return error */
				return $this->response->add( [
					'status'  => WP_Http::FORBIDDEN,
					'handle'  => "jwt_auth_bad_iss",
					'data'    => [],
					'message' => __( 'The iss do not match with this server', 'api' ),
				] )->dispatch();
			}

			/** So far so good, validate the user id in the token */
			if ( ! isset( $token->data->user->id ) ) {
				/** No user id in the token, abort! */
				return $this->response->add( [
					'status'  => WP_Http::FORBIDDEN,
					'handle'  => "jwt_auth_bad_request",
					'data'    => [],
					'message' => __( 'User ID not found in the token.', 'api' ),
				] )->dispatch();
			}

			/** If the output is true return an answer to the request to show it */
			return true;
		} catch ( \Exception $e ) {
			/** Something is wrong trying to decode the token, send back the error */
			return $this->response->add( [
				'status'  => WP_Http::FORBIDDEN,
				'handle'  => "jwt_auth_invalid_token",
				'data'    => [],
				'message' => $e->getMessage(),
			] )->dispatch();
		}
	}

	/**
	 * A Trick to get all methods from the Main Request.
	 *
	 * @param $method
	 * @param $arguments
	 *
	 * @return mixed
	 *
	 * @author rumur
	 */
	public function __call( $method, $arguments )
	{
		if ( is_callable( [ $this->request, $method ], true ) ) {
			return call_user_func_array( [ $this->request, $method ], $arguments );
		}
	}

}
