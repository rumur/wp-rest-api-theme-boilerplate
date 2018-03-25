<?php
/**
 * api functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package api
 */

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

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
	$title = $title ?: __('Theme &rsaquo; Error', 'api');
	$message = "<h1>{$title}<br><small>{$subtitle}</small></h1><p>{$message}</p>";
	wp_die($message, $title);
};

/**
 * Api required composer files.
 */
if ( ! file_exists( $composer = __DIR__ . '/vendor/autoload.php' ) ) {
	$compat_error(
		__( 'You must run <code>composer install</code> from the App WP Theme directory.', 'api' ),
		__( 'Autoloader not found.', 'api' )
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
	        $compat_error(sprintf(__('Error locating <code>%s</code> for inclusion.', 'api'), $file), 'File not found');
	    }
	}, $include_file_list );
} catch (Throwable $e) {
	$compat_error($e->getMessage(), 'Something bad happened :(');
}

add_action( 'after_setup_theme', function() {
  if ( ! empty( $_POST ) ) {

    $request = \App\app('request');

    $validation = new \App\Services\Validation\Validation( $request, [
      'username' => 'required|text|min:5',
      'password' => 'required|min:6|password:special',
      'test'  => 'bool|required'
    ]);

    $fields = (object) $validation->validate();

    tp($fields);

    dd($validation->hasFailedFields());
  }
}, 11);
