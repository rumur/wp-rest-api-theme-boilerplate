<?php

namespace App\Api\Controllers;

use App\Api\Model\User;
use App\Api\Services\Http\RequestFailException;
use App\Api\Services\Controller\BaseController;

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
        'password' => 'required',
        'username' => 'required',
    ];

    /**
     * Normalize rules for request.
     *
     * @param null $rulesFor
     * @return array
     *
     * @author rumur
     */
    protected function getFormRules( $rulesFor = null )
    {
        $rules = $this->form_rules;

        switch ( $rulesFor ) {
            case 'login':
                unset( $rules['email'] );
                break;
            case 'register':
                $rules['email'] .= '|unique:users,user_email';
                $rules['username'] .= '|min:5|unique:users,user_login';
                $rules['password'] .= '|min:6|regex:((?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20})';
                break;
        }

        return $rules;
    }

    /**
     * Login user.
     *
     * @return mixed|\WP_REST_Response
     *
     * @author rumur
     */
    public function login()
    {
        $validator = $this->validate( $this->getFormRules( 'login' ) );

        try {

            $failed_msg = __( 'User login is failed.', TEXT_DOMAIN );

            if ($validator->fails()) {
                throw new RequestFailException($failed_msg, $validator->errors()->toArray());
            }

            $fields = (object) $validator->getData();

            $user = wp_authenticate( $fields->username, $fields->password );

            if ( is_wp_error( $user ) ) {
                throw new RequestFailException($failed_msg, $user->get_error_data());
            } else {
                $payload = [
                    'handle'  => 'api_login_success',
                    'data'    => [
                        'login'    => $user->get( 'user_login' ),
                        'email'    => $user->get( 'user_email' ),
                        'username' => $user->get( 'user_nicename' ),
                    ],
                    'message' => __( 'User has been logged in successfully.', TEXT_DOMAIN ),
                ];

                return $this->response->add($payload)->ok();
            }

        } catch (RequestFailException $e) {
            return $this->response->add( $e->getResponseData() )->forbidden();
        }
    }

    /**
     * Register User.
     *
     * @return mixed|\WP_REST_Response
     *
     * @author rumur
     */
    public function register()
    {
        $extra_messages = [
            'password.regex' => __('Password must contain at least one number and both uppercase and lowercase letters.', TEXT_DOMAIN)
        ];

        $validator = $this->validate($this->getFormRules( 'register' ), $extra_messages);

        try {

            $failed_msg = __('Registration is failed.', TEXT_DOMAIN);

            if ($validator->fails()) {
                throw new RequestFailException($failed_msg, $validator->errors()->toArray());
            }

            $fields = (object) $validator->getData();

            $user = wp_create_user( $fields->username, $fields->password, $fields->email );

            if ( is_wp_error( $user ) ) {
                throw new RequestFailException($failed_msg, $user->get_error_data());
            } else {
                $user = get_user_by( 'id', $user );

                $payload = [
                    'handle' => 'api_registration_success',
                    'data' => [
                        'login' => $user->get('user_login'),
                        'email' => $user->get('user_email'),
                        'username' => $user->get('user_nicename'),
                    ],
                    'message' => __( 'Registration success.', TEXT_DOMAIN ),
                ];
            }

            return $this->response->add($payload)->ok();

        } catch (RequestFailException $e) {
            return $this->response->add( $e->getResponseData() )->forbidden();
        }
    }

    /**
     * Logout current user.
     *
     * @author rumur
     */
    public function logout()
    {
        $user = User::find(wp_get_current_user()->ID);

        wp_logout();

        return $this->response->add( [
            'handle'  => 'api_user_logout',
            'message' => sprintf( __( 'See you soon %s!', TEXT_DOMAIN ), $user->user_login ),
        ] )->ok();
    }

    /**
     * Gets info about an authorized user.
     *
     * @uses   WP_Http, RequestJWTAdapter
     *
     * @return mixed|\WP_REST_Response
     *
     * @author rumur
     */
    public function me()
    {
        if ($user = User::find(wp_get_current_user()->ID)) {
            $payload = [
                'handle' => 'api_user_valid',
                'data'   => [
                    'login'    => $user->user_login,
                    'email'    => $user->user_email,
                    'username' => $user->user_nicename,
                ],
            ];
        } else {
            $payload = [
                'handle'  => 'api_user_invalid',
                'data'    => [],
                'message' => __( 'User was not found. Log in once again.', TEXT_DOMAIN ),
            ];
        }

        return  $this->response->add($payload)->dispatch();
    }
}
