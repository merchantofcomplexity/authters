<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\EventAuthenticationMiddelware;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use MerchantOfComplexity\Authters\Support\Middleware\HasAuthenticationEvent;
use MerchantOfComplexity\Authters\Support\Middleware\HasAuthenticationMiddleware;
use Symfony\Component\HttpFoundation\Response;

abstract class AuthenticationMiddleware implements EventAuthenticationMiddelware
{
    use HasAuthenticationMiddleware, HasAuthenticationEvent;

    public function handle(Request $request)
    {
        if (!$this->requireAuthentication($request)) {
            return null;
        }

        try {
            $response = $this->processAuthentication($request);
        } catch (AuthenticationException $exception) {
            if ($this->dispatcher) {
                $this->fireFailureLoginEvent($request, $exception);
            }

            return $this->respond->entrypoint($request, $exception);
        }

        return $response;
    }

    abstract protected function requireAuthentication(Request $request): bool;

    abstract protected function processAuthentication(Request $request): ?Response;
}