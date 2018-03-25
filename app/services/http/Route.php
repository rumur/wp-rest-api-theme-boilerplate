<?php

namespace App\Services\Http;

use \WP_Http;
use App\Service\Middleware\MiddlewareInterface;

/**
 * Class Route
 * @package App\Services
 * @author  rumur
 */
class Route {
	/** @var string */
	const ACTION_DELIMITER = '@';

	/**
	 * Make a route for several methods.
	 *
	 * @param array  $methods
	 * @param        $namespace
	 * @param string $regexp
	 * @param array  $args
	 *
	 * @return array
	 *
	 * @author rumur
	 */
	static public function match( array $methods, $namespace, $regexp = '', array $args = [] )
	{
		return array_map( function ( $method ) use ( $namespace, $regexp, $args ) {
			return static::$method( $namespace, $regexp, $args );
		}, $methods );
	}

	/**
	 * The Fallback method.
	 *
	 * @param $name
	 * @param $arguments
	 *
	 * @return Route|\WP_Error
	 *
	 * @author rumur
	 */
	public static function __callStatic( $name, $arguments )
	{
		switch ( $name ) {
			case 'get':
			case 'readable':
				$methods = Server::READABLE;
				break;
			case 'post':
			case 'creatable':
				$methods = Server::CREATABLE;
				break;
			case 'delete':
			case 'deletable':
				$methods = Server::DELETABLE;
				break;
			case 'put':
				$methods = 'PUT'; // @TODO maybe use instead -> \WP_REST_Server::EDITABLE,
				break;
			case 'patch':
				$methods = 'PATCH'; // @TODO maybe use instead -> \WP_REST_Server::EDITABLE,
				break;
			case 'any':
				$methods = Server::ALLMETHODS;
				break;
			default:
				return _doing_it_wrong(
					__CLASS__ . '::' . $name,
					__( 'The wrong method provided.', 'api' ),
					null
				);
		}

		$self = new self();

		list( $namespace, $regexp, $args ) = $arguments;

		$args = $self->parseArgs( [
			'methods' => $methods,
		], $args );

		$self->register( $namespace, $regexp, $args );

		return $self;
	}

	/**
	 * Makes the route available for the REST API
	 *
	 * @param        $namespace     Route Namespace e.g. `todo\v1`
	 * @param string $regexp        Regexp
	 * @param array  $args          Compatible with \WP_REST_Server::register_route
	 *
	 * @return boolean
	 *
	 * @author rumur
	 */
	protected function register( $namespace, $regexp = '', array $args = [] )
	{
		return register_rest_route( $namespace, $regexp, $args );
	}

	/**
	 * Makes args eatable for `register_rest_route` function.
	 *
	 * @param array $args
	 * @param array $coming_args
	 *
	 * @return array
	 *
	 * @author rumur
	 */
	protected function parseArgs( array $args, array $coming_args )
	{
		$args = wp_parse_args( $args, $coming_args );

		try {
			// Making back compat.
			if ( isset( $args['use'] ) && ! isset( $args['callback'] ) ) {
				$args['callback'] = $args['use'];

				unset( $args['use'] );
			}

			if ( isset( $args['middleware'] ) ) {
				$this->injectMiddleware( $args );
			}

			$delimiter = self::ACTION_DELIMITER;

			// We're assuming that the route has a Class@method callback.
			$has_class_method = strpos( $args['callback'], $delimiter ) !== false;

			if ( $has_class_method ) {
				list( $class, $method ) = explode( $delimiter, $args['callback'] );

				$args['callback'] = function () use ( $class, $method ) {
					return call_user_func_array( [ new $class(), $method ], func_get_args() );
				};
			}
		} catch ( \Exception $e ) {
			//new \WP_Error( 'route_wrong_args_structure', $e->getMessage() );
		}

		return $args;
	}

	/**
	 * Injects the Middleware for permission check.
	 *
	 * @see http://v2.wp-api.org/extending/adding/#permissions-callback
	 *
	 * @param $args
	 *
	 * @author rumur
	 */
	protected function injectMiddleware( &$args )
	{
		if ( ! isset( $args['permission_callback'] ) ) {
			$middleware = $args['middleware'];

			unset( $args['middleware'] );

			$args['permission_callback'] = function () use ( $middleware ) {
				$validate_middleware = function ( $middleware, $args ) {
					$error_msg = __( 'Wrong Middleware interface provided.', 'api' );

					return $middleware instanceof MiddlewareInterface
						? call_user_func_array( [ $middleware, 'handle' ], $args  )
						: Response::make( [
							'handle'  => 'route_doing_wrong',
							'status'  => WP_Http::NOT_FOUND,
							'message' => $error_msg,
						] )->dispatch();
				};

				// Gather all arguments together for `permission_callback` function.
        // Args are passed from @see `wp-includes/rest-api.php:rest_send_allow_header`
        $callback_args = func_get_args();

				if ( is_array( $middleware ) ) {

					$stack = array_map( function ( $middleware ) use ( $validate_middleware, $callback_args ) {
						return $validate_middleware( new $middleware(), $callback_args );
					}, $middleware );

					$has_errors = array_filter( $stack, function ( $middleware_result ) {
						return is_wp_error( $middleware_result ) || $middleware_result === false;
					} );

					return empty( $has_errors )
						? true                                // it's ok we can go further.
						: array_shift( $has_errors ); // if has errors take the first error out.
				}

				return $validate_middleware( new $middleware(), $callback_args );
			};
		}
	}
}
