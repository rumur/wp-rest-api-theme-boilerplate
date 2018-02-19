<?php

namespace App\Services\Http;

use Firebase\JWT\JWT;
use WP_REST_Request as Request;
use App\Services\Transmit\APIError;

/**
 * Class Request
 * @package App\Services\Http
 * @author  rumur
 */
class RequestAdapter {
	/** @var Request */
	protected $request;
	/** @var string */
	private $token;

	/**
	 * RequestAdapter constructor.
	 *
	 * @param Request $request
	 */
	public function __construct( Request $request )
	{
		$this->request = $request;
	}

	/**
	 * @param Request $request
	 *
	 * @return RequestAdapter
	 *
	 * @author rumur
	 */
	public static function make( Request $request )
	{
		$self = new self( $request );

		return $self;
	}

	/**
	 * Gets token from the HTTP Headers.
	 * @return \WP_Error| String
	 *
	 * @author rumur
	 */
	public function getTokenFromRequest()
	{
		if ( $auth = $this->request->get_header( 'Authorization' ) ) {
			list( $token ) = sscanf( $auth, 'Bearer %s' );

			return $token
				? $token
				: APIError::make( 'api_no_auth_token', __( 'Authorization token is missed.', 'api' ))
				          ->setStatus( 403 )->transmit();
		}

		return APIError::make( 'api_no_auth_header', __( 'Authorization header not found.', 'api' ))
		               ->setStatus( 403 )->transmit();
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
	 * @return object
	 *
	 * @author rumur
	 */
	public function getDecodedToken()
	{
		$secret_key = defined('JWT_AUTH_SECRET_KEY') ? JWT_AUTH_SECRET_KEY : false;

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
		$secret_key = defined('JWT_AUTH_SECRET_KEY') ? JWT_AUTH_SECRET_KEY : false;
		$username = $this->request->get_param('username');
		$password = $this->request->get_param('password');

		/** First thing, check the secret key if not exist return a error*/
		if ( ! $secret_key ) {
			$msg = __( 'JWT is not configurated properly, please contact the admin', 'api' );

			return APIError::make( 'jwt_auth_bad_config', $msg )->setStatus( 403 )->transmit();
		}

		/** Try to authenticate the user with the passed credentials*/
		$user = wp_authenticate( $username, $password );

		/** If the authentication fails return a error*/
		if ( is_wp_error( $user ) ) {
			$error_code = $user->get_error_code();

			return APIError::make( '[jwt_auth] ' . $error_code, $user->get_error_message( $error_code ) )
			               ->setStatus( 403 )->transmit();
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
			return APIError::make( 'api_no_auth_token', __( 'Authorization token is missed.', 'api' ) )
			               ->setStatus( 403 )->transmit();
		}

		/** Get the Secret Key */
		$secret_key = defined('JWT_AUTH_SECRET_KEY') ? JWT_AUTH_SECRET_KEY : false;

		if ( ! $secret_key ) {
			$msg = __( 'JWT is not configurated properly, please contact the admin', 'api' );

			return APIError::make( 'jwt_auth_bad_config', $msg )->setStatus( 403 )->transmit();
		}

		/** Try to decode the token */
		try {
			$token = $this->getDecodedToken();

			/** The Token is decoded now validate the iss */
			if ( $token->iss != get_bloginfo( 'url' ) ) {
				/** The iss do not match, return error */
				return APIError::make( 'jwt_auth_bad_iss', __( 'The iss do not match with this server', 'api' ) )
				               ->setStatus( 403 )->transmit();
			}

			/** So far so good, validate the user id in the token */
			if ( ! isset( $token->data->user->id ) ) {
				/** No user id in the token, abort!! */
				return APIError::make( 'jwt_auth_bad_request', __( 'User ID not found in the token', 'api' ) )
				               ->setStatus( 403 )->transmit();
			}

			/** If the output is true return an answer to the request to show it */
			return true;
		} catch ( \Exception $e ) {
			/** Something is wrong trying to decode the token, send back the error */
			return APIError::make( 'jwt_auth_invalid_token', $e->getMessage() )
			               ->setStatus( 403 )->transmit();
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
	function __call( $method, $arguments )
	{
		if ( is_callable( [ $this->request, $method ], true ) ) {
			return call_user_func_array( [ $this->request, $method ], $arguments );
		}
	}

}