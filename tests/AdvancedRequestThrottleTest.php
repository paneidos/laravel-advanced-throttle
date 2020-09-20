<?php

namespace Paneidos\AdvancedThrottle\Tests;

use Illuminate\Cache\RateLimiter;
use Illuminate\Config\Repository;
use Illuminate\Http\Request;
use Mockery;
use Paneidos\AdvancedThrottle\AdvancedRequestThrottle;
use PHPUnit\Framework\TestCase;

class AdvancedRequestThrottleTest extends TestCase
{
    public function testIntegerValue(): void {
        $config = new Repository();
        $config->set('api.throttle', ['127.0.0.1' => -1]);
        $rateLimiter = Mockery::mock(RateLimiter::class);

        $throttle = new AdvancedRequestThrottle($rateLimiter, $config);
        $request = new Request([], [], [], [], [], [
            'REMOTE_ADDR' => '127.0.0.1',
        ], []);
        $throttle->handle($request, function() {
            $this->assertTrue(true);
        }, 60, 1, 'api.throttle');
        $rateLimiter->shouldReceive('tooManyAttempts')->andReturnUsing(function() {
            $this->assertTrue(false, 'Expected to skip rate limiter');
        });
    }

    public function testArrayValue(): void {
        $config = new Repository();
        $config->set('api.throttle', ['127.0.0.1' => ['limit' => -1]]);
        $rateLimiter = Mockery::mock(RateLimiter::class);

        $throttle = new AdvancedRequestThrottle($rateLimiter, $config);
        $request = new Request([], [], [], [], [], [
            'REMOTE_ADDR' => '127.0.0.1',
        ], []);
        $throttle->handle($request, function() {
            $this->assertTrue(true);
        }, 60, 1, 'api.throttle');
        $rateLimiter->shouldReceive('tooManyAttempts')->andReturnUsing(function() {
            $this->assertTrue(false, 'Expected to skip rate limiter');
        });
    }
}
