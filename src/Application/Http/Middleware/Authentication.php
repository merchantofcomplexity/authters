<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Firewall\Factory\HasGuard;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\AuthenticationGuard;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

abstract class Authentication implements AuthenticationGuard
{
    use HasGuard;

    public function authenticate(Request $request): ?Response
    {
        if (!$this->requireAuthentication($request)) {
            return null;
        }

        try {
            $response = $this->processAuthentication($request);
        } catch (AuthenticationException $exception) {
            if(method_exists($this, 'fireFailureLoginEvent')){
                $this->fireFailureLoginEvent($request, $exception);
            }

            return $this->guard->startAuthentication($request, $exception);
        }

        return $response;
    }

    abstract protected function requireAuthentication(Request $request): bool;

    abstract protected function processAuthentication(Request $request): ?Response;
}