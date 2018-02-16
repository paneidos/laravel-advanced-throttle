# Laravel Advanced Throttle

Throttle your API requests based on IP address. It's meant to be a drop-in replacement for the default throttle class provided by Laravel.

# Usage

`composer require paneidos/laravel-advanced-throttle`

# Config

## app/Http/Kernel.php

```php
<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel {
    /* SNIPPED */
    
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
        ],

        'api' => [
            // Change the third argument below if you like, but remember to update your config
            'throttle:60,1,api.throttle',
        ],
    ];
    
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.integration' => \App\Http\Middleware\AuthenticateIntegration::class,
        'auth.merchant' => \App\Http\Middleware\AuthenticateMerchantToken::class,
        'can' => \Illuminate\Foundation\Http\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        // 'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        // Replace the original line (commented out above) with the one below
        // (only needed if you don't use the service provider)
        'throttle' => \Paneidos\AdvancedThrottle\AdvancedRequestThrottle::class,
    ];
}
```

## config/api.php

You can publish the default config file, which sets requests from localhost to unlimited:

```
php artisan vendor:publish --provider="Paneidos\AdvancedThrottle\AdvancedThrottleServiceProvider" --tag=config
```

Or you can create your config manually:

```php
<?php

return [
    'throttle' => [
        '127.0.0.1' => ['limit' => 300, 'per' => 2], // 300 requests per 2 minutes
        '::2/64' => ['limit' => -1], // unlimited
        // Starting with Laravel 5.6, you can make limit a propery name of the user:
        '192.168.0.0/16' => ['limit' => 'rate_limit'],
    ],
];
```
