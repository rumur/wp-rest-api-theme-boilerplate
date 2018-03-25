<?php

namespace App;

use App\Service\AppContainer;

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
