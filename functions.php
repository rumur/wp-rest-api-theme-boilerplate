<?php
/**
 * api functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package api
 */

use App\Api\Config;
use App\Api\Service\Container;

defined('DS') || define('DS', DIRECTORY_SEPARATOR);
define('TEXT_DOMAIN', 'api');

/**
 * Loads all
 *
 * @param $dir
 * @param null $exclude_file
 */
function dir_loader( $dir, $exclude_file = null ) {
	$exclude_file = $exclude_file ? array( $exclude_file ) : array();

	$files = array_diff( glob( $dir . DS . '*.php' ), $exclude_file );

	array_map( function ( $filename ) {
		if ( file_exists( $filename ) ) {
			include $filename;
		}
	}, $files );
}

/**
 * Helper function for prettying up errors.
 *
 * @param string $message
 * @param string $subtitle
 * @param string $title
 */
$compat_error = function ($message, $subtitle = '', $title = '') {
	$title = $title ?: __('Theme &rsaquo; Error', TEXT_DOMAIN);
	$message = "<h1>{$title}<br><small>{$subtitle}</small></h1><p>{$message}</p>";
	wp_die($message, $title);
};

/**
 * Ensure compatible version of PHP is used
 */
if (version_compare('7.1', phpversion(), '>=')) {
    $compat_error(__('You must be using PHP 7 or greater.', TEXT_DOMAIN), __('Invalid PHP version', TEXT_DOMAIN));
}

/**
 * Ensure compatible version of WordPress is used
 */
if (version_compare('4.7.0', get_bloginfo('version'), '>=')) {
    $compat_error(__('You must be using WordPress 4.7.0 or greater.', TEXT_DOMAIN), __('Invalid WordPress version', TEXT_DOMAIN));
}

/**
 * Api required composer files.
 */
if ( ! file_exists( $composer = __DIR__ . '/vendor/autoload.php' ) ) {
	$compat_error(
		__( 'You must run <code>composer install</code> from the App WP Theme directory.', TEXT_DOMAIN ),
		__( 'Autoloader not found.', TEXT_DOMAIN )
	);
}

require_once $composer;

/**
 * The crucial file's list that need to be loaded.
 */
$include_file_list = apply_filters( 'app/load_crucial_file_list', [
	'helpers/loader',   // Theme helpers
	'hooks/loader',     // Theme actions
	'setup',            // Theme Setup
	'admin/loader',     // Loader for Admin stuff
] );

/**
 * Api required files
 *
 * The mapped array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 */
try {
	array_map(function ($file) use ($compat_error) {
	    $file = "./app/{$file}.php";
	    if (! locate_template($file, true, true)) {
	        $compat_error(sprintf(__('Error locating <code>%s</code> for inclusion.', TEXT_DOMAIN), $file), 'File not found');
	    }
	}, $include_file_list );
} catch (Throwable $e) {
	$compat_error($e->getMessage(), 'Something bad happened :(');
}

/**
 * Adds Configs to the App.
 */
Container::getInstance()
    ->bindIf('config', function () {
        return new Config([
            'theme' => require dirname(__FILE__) . '/config/theme.php',
            'rest' => require dirname(__FILE__) . '/config/rest.php',
        ]);
    }, true);
