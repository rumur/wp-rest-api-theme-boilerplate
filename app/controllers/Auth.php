<?php

namespace App\Controllers;
use App\Services\Http\RequestAdapter;
use App\Services\Transmit\APIResponse;

/**
 * Class Auth
 * @package App\Controllers
 * @author  rumur
 */
class Auth extends BaseController {
	/**
	 * Login user.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed
	 *
	 * @author rumur
	 */
	public function login( \WP_REST_Request $request )
	{
		$fields = (object) $this->validate( $request, [
			'username' => 'required|text|min:5',
			'password' => 'required',
		] );

		if ( $errors = $this->hasFailedValidation() ) {
			return APIResponse::make( 'api_login_failed', $errors )->setStatus( 403 )->transmit();
		}

		$user = wp_authenticate( $fields->username, $fields->password );

		if ( ! is_wp_error( $user ) ) {
			$response_data = RequestAdapter::make( $request )->generateTokenData();

			$response_data['message'] = __( 'User has been logged in successfully.', 'api' );

			return APIResponse::make( 'api_login_success', $response_data )->setStatus( 200 )->transmit();
		} else {
			return APIResponse::make( 'api_login_failed', $user->get_error_message() )
			                  ->setStatus( 403 )->transmit();
		}
	}

	/**
	 * Register User.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 *
	 * @author rumur
	 */
	public function register( \WP_REST_Request $request )
	{
		$fields = (object) $this->validate( $request, [
			'email'    => 'required|email',
			'username' => 'required|text|min:5',
			'password' => 'required|min:3',//'required|password:num,upper,special|min:6',
		] );

		$errors = array_fill_keys( [ 'email', 'username', 'password' ], '' );

		if ( $this->hasFailedValidation() ) {
			$errors = $this->hasFailedValidation();
			
			isset( $errors['username'] )
				&& $errors['username'] = $errors['username'] . " " . __( 'Username should me more or equal 5 characters.', 'api' );

			isset( $errors['password'] )
				&& $errors['password'] = $errors['password'] . " " . __( 'Password should be more than 6 symbols.', 'api' );

			return APIResponse::make( 'api_registration_failed', $errors )->setStatus( 403 )->transmit();
		}

		email_exists( $fields->email )
			&& $errors['email'] = __( 'Email exists.', 'api' );

		username_exists( $fields->username )
			&& $errors['username'] = __( 'User exists.', 'api' );

		$hasErrors = array_filter( $errors );

		if ( $hasErrors ) {
			return APIResponse::make( 'api_registration_invalid', $errors )->setStatus( 422 )->transmit();
		}

		$user = wp_create_user( $fields->username, $fields->password, $fields->email );

		if ( ! is_wp_error( $user ) ) {
			$response_data = RequestAdapter::make( $request )->generateTokenData();

			// @TODO needs to getting the token here.
			return APIResponse::make( 'api_registration_success', $response_data )->setStatus( 200 )->transmit();
		} else {
			return APIResponse::make( 'api_registration_invalid', $user->get_error_message() )
			                  ->setStatus( 422 )->transmit();
		}
	}

	/**
	 * Gets info about an authorized user.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 *
	 * @author rumur
	 */
	public function me( \WP_REST_Request $request )
	{
		$r = RequestAdapter::make( $request );

		$is_token_valid = ! is_wp_error( $r->getTokenFromRequest() )
		                  && ! is_wp_error( $r->validateToken() );

		if ( $is_token_valid ) {
			$data = $r->getDecodedToken();

			if ( isset( $data->user->id ) ) {
				$user = get_user_by( 'id', absint( $data->user->id ) );

				if ( ! is_wp_error( $user ) ) {
					$response_data = [
						'token' => $r->getToken(),
						'user_email' => $user->data->user_email,
						'user_nicename' => $user->data->user_nicename,
						'user_display_name' => $user->data->display_name,
					];

					return APIResponse::make( 'api_get_token_successful', $response_data )
					                  ->setStatus( 200 )->transmit();
				}

				return APIResponse::make( 'api_get_token_failed', __( 'User was not found', 'api' ) )
				                  ->setStatus( 403 )->transmit();
			}
		}

		return APIResponse::make( 'api_get_token_failed', __( 'Invalid Token.', 'api' ) )
		                  ->setStatus( 403 )->transmit();
	}
}