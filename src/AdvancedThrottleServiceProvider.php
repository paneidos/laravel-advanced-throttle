<?php

namespace Paneidos\AdvancedThrottle;

use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\ServiceProvider;

class AdvancedThrottleServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ThrottleRequests::class, AdvancedRequestThrottle::class);
    }
}
