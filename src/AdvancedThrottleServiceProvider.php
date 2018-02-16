<?php

namespace Paneidos\AdvancedThrottle;

use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\ServiceProvider;

class AdvancedThrottleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $configPath = __DIR__ . '/../config/api.php';
        if (function_exists('config_path')) {
            $publishPath = config_path('api.php');
        } else {
            $publishPath = base_path('config/api.php');
        }
        $this->publishes([$configPath => $publishPath], 'config');
    }

    public function register()
    {
        $this->app->bind(ThrottleRequests::class, AdvancedRequestThrottle::class);
    }
}
