<?php

namespace App;

use App\Services\Assets;
use App\Api\Service\Container;

/**
 * Get the app container.
 *
 * @param string $abstract
 * @param array  $parameters
 * @param Container $container
 * @return Container|mixed
 */
function app($abstract = null, $parameters = [], Container $container = null)
{
    $container = $container ?: Container::getInstance();

    if (! $abstract) {
        return $container;
    }

    return $container->bound($abstract)
        ? $container->makeWith($abstract, $parameters)
        : $container->makeWith("app.{$abstract}", $parameters);
}

/**
 * Get / set the specified configuration value.
 *
 * If an array is passed as the key, we will assume you want to set an array of values.
 *
 * @param array|string $key
 * @param mixed $default
 * @return mixed|\App\Api\Config
 * @copyright Taylor Otwell
 * @link https://github.com/laravel/framework/blob/c0970285/src/Illuminate/Foundation/helpers.php#L254-L265
 */
function config($key = null, $default = null)
{
    if (is_null($key)) {
        return app('config');
    }
    if (is_array($key)) {
        return app('config')->set($key);
    }

    return app('config')->get($key, $default);
}

/**
 * Gets the right file uri from dist folder.
 *
 * @param string $relative_asset_path Path to the dist file
 *                                    e.g. `scripts/main.js` | `images/EasyPeasy.png`
 *
 * @return string
 */
function asset($relative_asset_path)
{
    $dist_uri = get_theme_file_uri() . '/dist';
    $manifest_path = get_theme_file_path() . '/dist/assets.json';

    return Assets::make($manifest_path, $dist_uri)->getUri($relative_asset_path);
}
