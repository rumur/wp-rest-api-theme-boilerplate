# WP Theme for REST API only.

### The `WP REST API` theme boilerplate.

### Includes
 - the primitive `Routing`, `Controller`, `Middleware`.
 - controllers use [Laravel's Validation](https://laravel.com/docs/5.6/validation#available-validation-rules) system
 - [Eloquent](https://laravel.com/docs/5.6/eloquent) on board 

### Routing sample.

```
// /app/api/routes.php
Route::post( 'todo/v1', 'me', [
	'use' => '\App\Controllers\Auth@me',
	'middleware' => \App\Middleware\JWT::class,
]);

Route::get( 'todo/v1', 'tasks', [
	'use' => '\App\Controllers\Tasks@list',
	'middleware' => [ 
	    \App\Middleware\JWT::class,
	    \App\Middleware\Ownership::class,
    ]
]);

Route::any( 'todo/v1', 'events', [
	'use' => '\App\Controllers\Events@list',
]);

```

##### Also You can make a group of routes

```
// /app/api/routes.php

Route::group('auth/v1', [
    \App\Api\Middleware\LoggedOutMiddleware::class,
], function($namespace) {
    /**
     * Router is serving for register
     *
     * @since v1.0.0
     */
    Route::post( $namespace, 'register', [
        'use' => '\App\Api\Controllers\AuthController@register',
    ]);

    /**
     * Router is serving for login
     *
     * @since v1.0.0
     */
    Route::post( $namespace, 'login', [
        'use' => '\App\Api\Controllers\AuthController@login',
    ]);
});

```
### Includes a primitive `Controller` and Laravel `Validation` system. 
```
// /app/api/controllers/AuthController.php
class AuthController extends BaseController {
    // ...
    
    /**
     * Rules config for user fields.
     * @var array
     */
    protected $form_rules = [
        'email'    => 'required|email|unique:users,user_email',
        'password' => 'required|min:6|regex:((?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20})',
        'username' => 'required|unique:users,user_login',
    ];
    
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
        
            if ($validator->fails()) {
                throw new RequestFailException(__('Registration is failed.', TEXT_DOMAIN), $validator->errors()->toArray());
            }
            
            // ...
        } catch (RequestFailException $e) {
            return $this->response->add( $e->getResponseData() )->forbidden();
        }
    }
    
    // ...
}
```

## Composer Setup

`$ git clone the repo`

`$ cd to/dir/where/theme/is/`

`$ composer install`

