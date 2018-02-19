# WP Theme for REST API only.

### The Simple JWT ready `WP REST API` theme boilerplate.
### Includes the primitive `Routing` system.

### Routing sample.

```
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

### Includes the primitive `Controller` and  `Validation` systems. 
```
class Auth extends BaseController {
    // ...
    
    public function register( \WP_REST_Request $request )
    {
        $fields = (object) $this->validate( $request, [
            'email'    => 'required|email',
            'username' => 'required|text|min:5',
            'password' => 'required|password:num,upper,special|min:6',
        ] );
        
        // ...
    }
    
    // ...
}
```

## Composer Setup

`$ git clone the repo`.

`$ cd to/dir/where/theme/is/`

`$ composer install`

