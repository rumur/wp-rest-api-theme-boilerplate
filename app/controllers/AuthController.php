<?php

namespace App\Controllers;

use \WP_Http;
use App\Services\Controller\BaseController;


/**
 * Class AuthController
 * @package App\Controllers
 * @author  rumur
 */
class AuthController extends BaseController {
	/**
	 * Rules config for user fields.
	 * @var array
	 */
	protected $form_rules = [
		'email'    => 'required|email',
		'password' => 'required|min:5',
		'username' => 'required|text|min:5',
	];

	/** @inheritdoc */
	protected function getFormRules($rulesFor = null)
	{
		$rules = $this->form_rules;

		switch ( $rulesFor ) {
			case 'login':
				unset( $rules['email'] );
				break;
		}

		return $rules;
	}

	/**
	 * Login user.
	 *
	 * @return mixed
	 *
	 * @author rumur
	 */
	public function login()
	{
		$fields = (object) $this->validate( $this->getFormRules( __METHOD__ ) );

		if ( $errors = $this->hasFailedValidation() ) {
			return $this->response->add( [
				'status'  => WP_Http::FORBIDDEN,
				'handle'  => 'api_login_failed',
				'data'    => $errors,
				'message' => __( 'User login is failed.', 'api' )
			] )->dispatch();
		}

		$user = wp_authenticate( $fields->username, $fields->password );

		if ( is_wp_error( $user ) ) {
			return $this->response->add( [
				'status'  => WP_Http::FORBIDDEN,
				'handle'  => 'api_login_failed',
				'data'    => $user->get_error_data(),
				'message' => $user->get_error_messages(),
			] )->dispatch();
		}

		$response_data = $this->request->generateTokenData();

		return is_wp_error( $response_data )
			? $response_data
			: $this->response->add( [
				'status'  => WP_Http::OK,
				'handle'  => 'api_login_success',
				'data'    => $response_data,
				'message' => __( 'User has been logged in successfully.', 'api' ),
			] )->dispatch();
	}

	/**
	 * Register User.
	 *
	 * @return \WP_REST_Response
	 *
	 * @author rumur
	 */
	public function register()
	{
		$fields = (object) $this->validate( $this->getFormRules( __METHOD__ ) );

		$errors = array_fill_keys( [ 'email', 'username', 'password' ], '' );

		if ( $this->hasFailedValidation() ) {
			$errors = $this->hasFailedValidation();

			isset( $errors['username'] )
				&& $errors['username'] = $errors['username'] . " " . __( 'Username should be more or equal 5 characters.', 'api' );

			isset( $errors['password'] )
				&& $errors['password'] = $errors['password'] . " " . __( 'Password should be more than 6 symbols.', 'api' );

			return $this->response->add( [
				'status'  => WP_Http::FORBIDDEN,
				'handle'  => 'api_registration_failed',
				'data'    => $errors,
				'message' => __( 'Registration is failed.', 'api' ),
			] )->dispatch();
		}

		email_exists( $fields->email )
			&& $errors['email'] = __( 'Email exists.', 'api' );

		username_exists( $fields->username )
			&& $errors['username'] = __( 'User exists.', 'api' );

		$hasErrors = array_filter( $errors );

		if ( $hasErrors ) {
			return $this->response->add( [
				'status'  => WP_Http::UNPROCESSABLE_ENTITY,
				'handle'  => 'api_registration_invalid',
				'data'    => $errors,
				'message' => __( 'Registration is failed.', 'api' ),
			] )->dispatch();
		}

		$user = wp_create_user( $fields->username, $fields->password, $fields->email );

		if ( ! is_wp_error( $user ) ) {
			$response_data = $this->request->generateTokenData();

			// @TODO needs to getting the token here.
			return $this->response->add( [
				'status'  => WP_Http::OK,
				'handle'  => 'api_registration_success',
				'data'    => $response_data,
				'message' => __( 'Registration success.', 'api' ),
			] )->dispatch();
		} else {
			return $this->response->add( [
				'status'  => WP_Http::UNPROCESSABLE_ENTITY,
				'handle'  => 'api_registration_invalid',
				'data'    => $user->get_error_message(),
				'message' => __( 'Registration is failed.', 'api' ),
			] )->dispatch();
		}
	}

	/**
	 * Gets info about an authorized user.
	 *
	 * @uses WP_Http, RequestJWTAdapter
	 *
	 * @return \WP_REST_Response
	 *
	 * @author rumur
	 */
	public function me()
	{
		$is_token_valid = ! is_wp_error( $this->request->getTokenFromRequest() )
		                  && ! is_wp_error( $this->request->validateToken() );

		if ( $is_token_valid ) {
			$data = $this->request->getDecodedToken();

			if ( isset( $data->user->id ) ) {
				$user = get_user_by( 'id', absint( $data->user->id ) );

				if ( ! is_wp_error( $user ) ) {
					$response_data = [
						'token' => $this->request->getToken(),
						'user_email' => $user->data->user_email,
						'user_nicename' => $user->data->user_nicename,
						'user_display_name' => $user->data->display_name,
					];

					return $this->response->add( [
						'status' => WP_Http::OK,
						'handle' => 'api_get_token_successful',
						'data'	 => $response_data,
					] )->dispatch();
				}

				return $this->response->add( [
					'status' => WP_Http::FORBIDDEN,
					'handle' => 'api_get_token_failed',
					'data'	 => __( 'User was not found', 'api' ),
				] )->dispatch();
			}
		}

		return $this->response->add( [
			'status' => WP_Http::FORBIDDEN,
			'handle' => 'api_get_token_failed',
			'data'	 => __( 'Invalid Token.', 'api' ),
		] )->dispatch();
	}
}
