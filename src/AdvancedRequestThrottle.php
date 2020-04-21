<?php

namespace Paneidos\AdvancedThrottle;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Symfony\Component\HttpFoundation\IpUtils;

class AdvancedRequestThrottle extends ThrottleRequests
{
    /**
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    public function __construct(RateLimiter $limiter, Repository $config)
    {
        parent::__construct($limiter);
        $this->config = $config;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param int|string $maxAttempts
     * @param int $decayMinutes
     * @param string|null $config
     * @return mixed
     */
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1, $config = null)
    {
        if ($config && ($limits = $this->config->get($config))) {
            foreach ($limits as $ip => $limit) {
                if (IpUtils::checkIp($request->getClientIp(), $ip)) {
                    if (is_numeric($limit)) {
                        $maxAttempts = $limit;
                    } elseif (is_array($limit)) {
                        $maxAttempts = $limit['limit'] ?? $maxAttempts;
                        if (isset($limit['per'])) {
                            $decayMinutes = $limit['per'];
                        }
                    }
                    if ($maxAttempts < 0) {
                        return $next($request);
                    }
                    break;
                }
            }
        }
        return parent::handle($request, $next, $maxAttempts, $decayMinutes);
    }
}
