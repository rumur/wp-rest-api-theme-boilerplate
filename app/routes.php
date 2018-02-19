<?php
use App\Services\Http\Route;

$namespace = 'todo/v1';

/**
 * Router is serving for register
 *
 * @since v1.0.0
 */
Route::post( $namespace, 'register', [
	'use' => '\App\Controllers\Auth@register',
]);

/**
 * Router is serving for login
 *
 * @since v1.0.0
 */
Route::post( $namespace, 'login', [
	'use' => '\App\Controllers\Auth@login',
]);

/**
 * Router is serving for logout
 *
 * @since v1.0.0
 */
Route::post( $namespace, 'logout', [
	'use' => '\App\Controllers\Auth@logout',
	'middleware' => \App\Middleware\JWT::class, // <- Middleware can swallow an array as well.
]);

/**
 * Router is serving for getting the user data
 *
 * @since v1.0.0
 */
Route::post( $namespace, 'me', [
	'use' => '\App\Controllers\Auth@me',
	'middleware' => \App\Middleware\JWT::class,
]);
