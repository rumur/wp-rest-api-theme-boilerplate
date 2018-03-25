<?php

namespace App;

use App\Services\Http\Request;
use App\Services\Http\Response;

/**
 * Install the DI Container.
 */
add_action( 'after_setup_theme', function() {
  $container = app();

  // the same instance returns always.
  $container['request'] = function () {
    return new Request( $_SERVER['REQUEST_METHOD'], '/' );
  };
  // new instance returns always.
  $container['response'] = $container->factory( function () {
    return new Response();
  } );
});
