<?php
use App\Services\Http\Route;

$slug = 'todo';
$version = 1;

$namespace = "{$slug}/v{$version}";

/**
 * Router is serving for register
 *
 * @since v1.0.0
 */
Route::post( $namespace, 'register', [
	'use' => '\App\Controllers\AuthController@register',
]);

/**
 * Router is serving for login
 *
 * @since v1.0.0
 */
Route::post( $namespace, 'login', [
	'use' => '\App\Controllers\AuthController@login',
]);

/**
 * Router is serving for logout
 *
 * @since v1.0.0
 */
Route::post( $namespace, 'logout', [
	'use' => '\App\Controllers\AuthController@logout',
	'middleware' => \App\Middleware\JWTMiddleware::class, // <- Middleware can swallow an array as well.
]);

/**
 * Router is serving for getting the user data and refresh a token.
 *
 * @since v1.0.0
 */
Route::post( $namespace, 'me', [
	'use' => '\App\Controllers\AuthController@me',
	'middleware' => \App\Middleware\JWTMiddleware::class,
]);
