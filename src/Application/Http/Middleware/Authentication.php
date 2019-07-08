<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\EventAuthenticationAware;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use MerchantOfComplexity\Authters\Support\Middleware\HasAuthenticationEvent;
use MerchantOfComplexity\Authters\Support\Middleware\HasAuthentication;
use Symfony\Component\HttpFoundation\Response;

abstract class Authentication implements EventAuthenticationAware
{
    use HasAuthentication, HasAuthenticationEvent;

    public function handle(Request $request): ?Response
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