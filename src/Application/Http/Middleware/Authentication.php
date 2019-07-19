<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Guard\HasGuard;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\AuthenticationGuard;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

abstract class Authentication implements AuthenticationGuard
{
    use HasGuard;

    public function authenticate(Request $request, Closure $next)
    {
        if (!$this->requireAuthentication($request)) {
            return $next($request);
        }

        $response = null;

        try {
            $response = $this->processAuthentication($request);
        } catch (AuthenticationException $exception) {
            if (method_exists($this, 'fireFailureLoginEvent')) {
                $this->fireFailureLoginEvent($request, $exception);
            }

            return $this->guard->startAuthentication($request, $exception);
        }

        return $response ?? $next($request);
    }

    abstract protected function requireAuthentication(Request $request): bool;

    abstract protected function processAuthentication(Request $request): ?Response;
}