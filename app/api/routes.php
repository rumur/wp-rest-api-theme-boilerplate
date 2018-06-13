<?php
use App\Api\Services\Http\Route;


/**
 * Register Auth Group routes for non Logged in Users only.
 */
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


/**
 * Register Auth Group routes for Logged in Users only.
 */
Route::group('auth/v1', [
    \App\Api\Middleware\LoggedInMiddleware::class,
], function($namespace) {
    /**
     * Router is serving for getting the user data.
     *
     * @since v1.0.0
     */
    Route::get( $namespace, 'me', [
        'use' => '\App\Api\Controllers\AuthController@me',
    ]);

    /**
     * Router is serving for logout
     *
     * @since v1.0.0
     */
    Route::post( $namespace, 'logout', [
        'use' => '\App\Api\Controllers\AuthController@logout',
    ]);
});
