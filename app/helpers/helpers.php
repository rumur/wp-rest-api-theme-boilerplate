<?php

namespace App;

use App\Service\AppContainer;
use App\Services\Assets;

/**
 * Get the app container.
 *
 * @param string $abstract
 *
 * @param AppContainer $container
 * @return \Pimple\Container|mixed
 */
function app($abstract = null, AppContainer $container = null)
{
  $container = $container ?: AppContainer::getInstance()->getProvider();

  if (! $abstract) {
    return $container;
  }

  return $container->offsetExists($abstract)
    ? $container->offsetGet($abstract)
    : null;
}

/**
 * Gets the right file uri from dist folder.
 *
 * @param string $relative_asset_path Path to the dist file `scripts/main.js` | `images/EasyPeasy.png`
 * @return string
 */
function asset($relative_asset_path)
{
    $dist_uri = get_theme_file_uri() . '/dist';
    $manifest_path = get_theme_file_path() . '/dist/assets.json';

    return Assets::make($manifest_path, $dist_uri)->getUri($relative_asset_path);
}
