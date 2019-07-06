<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\GenericLocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\StatefulAuthenticationMiddleware as BaseStatefulMiddleware;
use MerchantOfComplexity\Authters\Support\Exception\BadCredentials;
use Symfony\Component\HttpFoundation\Response;

final class LocalAuthenticationMiddleware extends AuthenticationMiddleware implements BaseStatefulMiddleware
{
    /**
     * @var $recaller
     */
    private $recaller;

    protected function processAuthentication(Request $request): ?Response
    {
        [$email, $password] = $this->extractCredentials($request);

        $token = new GenericLocalToken($email, $password);

        $this->fireAttemptLoginEvent($request, $token);

        $authenticatedToken = $this->storeAuthenticatedToken($token);

        $this->fireSuccessLoginEvent($authenticatedToken, $request);

        $response = $this->respond->onSuccess($request, $authenticatedToken);

        if ($this->recaller) {
            //
        }

        return $response;
    }

    protected function extractCredentials(Request $request): array
    {
        [$identifier, $credentials] = $this->requestMatcher->extractCredentials($request);

        if (!$identifier || !$credentials) {
            throw BadCredentials::invalid();
        }

        return [$identifier, $credentials];
    }

    protected function requireAuthentication(Request $request): bool
    {
        if ($this->tokenStorage->getToken()) {
            return false;
        }

        return $this->requestMatcher->match($request);
    }

    public function setRecaller($recaller): void
    {
        $this->recaller = $recaller;
    }
}