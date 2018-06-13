<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Theme Directory
    |--------------------------------------------------------------------------
    |
    | This is the absolute path to your theme directory.
    |
    | Example:
    |   /srv/www/example.com/current/web/app/themes/api
    |
    */

    'dir' => $dir = get_theme_file_path(),

    /*
    |--------------------------------------------------------------------------
    | Theme Directory URI
    |--------------------------------------------------------------------------
    |
    | This is the web server URI to your theme directory.
    |
    | Example:
    |   https://example.com/app/themes/api
    |
    */

    'uri' => get_theme_file_uri(),

    /*
   |--------------------------------------------------------------------------
   | Theme Lang Directories
   |--------------------------------------------------------------------------
   |
   | This is the absolute path to your theme lang settings such as path.
   |
   | Example:
   |   /srv/www/example.com/current/web/app/themes/api/lang
   |
   */
    'lang' => [
        'path' => $dir . '/lang',
        'code' => '/',
    ],
];
