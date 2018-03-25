# WP Theme for REST API only.

### The Simple `WP REST API` theme boilerplate.
### Includes the primitive `Routing`, `Controller`, `Middleware` systems.

### Routing sample.

```
@ /app/routes.php
// ...
Route::post( 'todo/v1', 'me', [
	'use' => '\App\Controllers\Auth@me',
	'middleware' => \App\Middleware\JWT::class,
]);
// ...
Route::get( 'todo/v1', 'tasks', [
	'use' => '\App\Controllers\Tasks@list',
	'middleware' => [ 
	    \App\Middleware\JWT::class,
	    \App\Middleware\Ownership::class,
    ]
]);
// ...
Route::any( 'todo/v1', 'events', [
	'use' => '\App\Controllers\Events@list',
]);

```

### Includes a primitive `Controller` and `Validation` systems. 
```
@ /app/controllers/AuthController.php
class AuthController extends BaseController {
    // ...
    
    /**
     * Rules config for user fields.
     * @var array
     */
    protected $form_rules = [
        'email'    => 'required|email',
        'password' => 'required|min:5',
        'username' => 'required|text|min:5',
    ];
    
    public function register()
    {
        $fields = (object) $this->validate($this->form_rules);
        
        // ...
    }
    
    // ...
}
```

## Composer Setup

`$ git clone the repo`.

`$ cd to/dir/where/theme/is/`

`$ composer install`

