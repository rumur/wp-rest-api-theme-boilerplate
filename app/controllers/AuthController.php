<?php

namespace App\Controllers;

use \WP_Http;
use App\Services\Http\Response;
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
    protected function getFormRules( $rulesFor = null )
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
     * @return Response::$driver
     *
     * @author rumur
     */
    public function login()
    {
        $fields = (object) $this->validate( $this->getFormRules( __METHOD__ ) );

        if ( $errors = $this->hasFailedValidation() ) {
            $response_data = [
                'status'  => WP_Http::FORBIDDEN,
                'handle'  => 'api_login_failed',
                'data'    => $errors,
                'message' => __( 'User login is failed.', 'api' )
            ];
        } else {

            $user = wp_authenticate( $fields->username, $fields->password );

            if ( is_wp_error( $user ) ) {
                $response_data = [
                    'status'  => WP_Http::FORBIDDEN,
                    'handle'  => 'api_login_failed',
                    'data'    => $user->get_error_data(),
                    'message' => $user->get_error_messages(),
                ];
            } else {
                $response_data = [
                    'status'  => WP_Http::OK,
                    'handle'  => 'api_login_success',
                    'data'    => [
                        'login'    => $user->get( 'user_login' ),
                        'email'    => $user->get( 'user_email' ),
                        'username' => $user->get( 'user_nicename' ),
                    ],
                    'message' => __( 'User has been logged in successfully.', 'api' ),
                ];
            }
        }

        return $this->response->add( $response_data )->dispatch();
    }

    /**
     * Register User.
     *
     * @return Response::$driver
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
                'data'    => $hasErrors,
                'message' => __( 'Registration is failed.', 'api' ),
            ] )->dispatch();
        }

        $user = wp_create_user( $fields->username, $fields->password, $fields->email );

        if ( is_wp_error( $user ) ) {
            $response_data = [
                'status'  => WP_Http::FORBIDDEN,
                'handle'  => 'api_registration_invalid',
                'data'    => $user->get_error_data(),
                'message' => __( 'Registration is failed.', 'api' ),
            ];
        } else {

            $user = get_user_by( 'id', $user );

            $response_data = [
                'status'  => WP_Http::OK,
                'handle'  => 'api_registration_success',
                'data'    => [
                    'login'    => $user->get( 'user_login' ),
                    'email'    => $user->get( 'user_email' ),
                    'username' => $user->get( 'user_nicename' ),
                ],
                'message' => __( 'Registration success.', 'api' ),
            ];
        }

        return $this->response->add( $response_data )->dispatch();
    }

    /**
     * Gets info about an authorized user.
     *
     * @uses   WP_Http, RequestJWTAdapter
     *
     * @return Response::$driver
     *
     * @author rumur
     */
    public function me()
    {
        if ( is_user_logged_in() && ( $user = wp_get_current_user() ) ) {
            $response_data = [
                'status' => WP_Http::OK,
                'handle' => 'api_user_valid',
                'data'   => [
                    'login'    => $user->get( 'user_login' ),
                    'email'    => $user->get( 'user_email' ),
                    'username' => $user->get( 'user_nicename' ),
                ],
            ];
        } else {
            $response_data = [
                'status'  => WP_Http::FORBIDDEN,
                'handle'  => 'api_user_invalid',
                'data'    => [],
                'message' => __( 'User was not found. Log in once again.', 'api' ),
            ];
        }

        return $this->response->add( $response_data )->dispatch();
    }

    /**
     * Logout current user.
     *
     * @author rumur
     */
    public function logout()
    {
        $user = wp_get_current_user();

        wp_logout();

        return $this->response->add( [
            'status'  => WP_Http::OK,
            'handle'  => 'api_user_logout',
            'message' => sprintf( __( 'See you soon %s!', 'api' ), $user->get( 'user_nicename' ) ),
        ] )->dispatch();
    }
}
