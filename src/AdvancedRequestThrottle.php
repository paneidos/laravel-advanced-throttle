<?php

namespace Paneidos\AdvancedThrottle;

use Closure;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\IpUtils;

class AdvancedRequestThrottle extends ThrottleRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param int $maxAttempts
     * @param int $decayMinutes
     * @param string|null $config
     * @return mixed
     */
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1, $config = null)
    {
        if ($config && ($limits = Config::get($config))) {
            foreach($limits as $ip => $limit) {
                if (IpUtils::checkIp($request->getClientIp(), $ip)) {
                    if (isset($limit['limit']) && $limit['limit'] < 0) {
                        return $next($request);
                    }
                    if (isset($limit['limit'])) {
                        $maxAttempts = $limit['limit'];
                    }
                    if (isset($limit['per'])) {
                        $decayMinutes = $limit['per'];
                    }
                    break;
                }
            }
        }
        return parent::handle($request, $next, $maxAttempts, $decayMinutes);
    }
}