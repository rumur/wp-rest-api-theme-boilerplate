<?php

namespace App;

use App\Api\Services\Http\Request;
use App\Api\Services\Http\Response;

/**
 * Install the DI Container.
 */
add_action( 'after_setup_theme', function() {

    /**
     * Illuminate database.
     */
    $capsule = new \Illuminate\Database\Capsule\Manager(app());

    $db_args = [
        'driver' => 'mysql',
        'host' => DB_HOST,
        'database' => DB_NAME,
        'username' => DB_USER,
        'charset' => DB_CHARSET,
        'password' => DB_PASSWORD,
        'prefix' => defined('DB_PREFIX') ? DB_PREFIX : 'wp_',
    ];

    if (defined('DB_COLLATE') && DB_COLLATE) {
        $db_args['collation'] = DB_COLLATE;
    }

    $capsule->addConnection($db_args);
    //$capsule->setAsGlobal();
    $capsule->bootEloquent();

    /**
     * Make a connection the Eloquent to the DB.
     */
    app()->singleton('app.db', function () use ($capsule) {
        return $capsule;
    });

    /**
     * Register Eloquent features for Validator.
     *
     * @see \App\Api\Services\Validation\ValidationTrait::validate()
     */
    app()->singleton('app.validation.presence', function () {
        $resolver = \Illuminate\Database\Eloquent\Model::getConnectionResolver();

        if ($resolver instanceof \Illuminate\Database\ConnectionResolverInterface) {
            return new \Illuminate\Validation\DatabasePresenceVerifier($resolver);
        }
    });

    /**
     * Create a new Request instance and register it.
     * By providing an instance, the instance is shared.
     */
    app()->singleton('app.request', function () {
        return Request::capture();
    });

    /**
     * Add Response to App container.
     *
     * returns always a new instance of Response class
     */
    app()->bind('app.response', function () {
        return new Response();
    });
});

