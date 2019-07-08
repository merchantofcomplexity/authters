<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\GenericLocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Domain\LocalIdentity;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\LocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentityEmail;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use MerchantOfComplexity\Authters\Support\Exception\AuthtersValueFailure;
use MerchantOfComplexity\Authters\Support\Exception\BadCredentials;
use MerchantOfComplexity\Authters\Support\Middleware\HasAuthenticationEvent;
use MerchantOfComplexity\Authters\Support\Middleware\HasAuthentication;
use Symfony\Component\HttpFoundation\Response;

final class HttpBasicAuthentication extends Authentication
{
    use HasAuthentication, HasAuthenticationEvent;

    protected function processAuthentication(Request $request): ?Response
    {
        try {
            $token = $this->createToken($request);

            $this->fireAttemptLoginEvent($request, $token);

            $authenticatedToken = $this->storeAuthenticatedToken($token);

            $this->fireSuccessLoginEvent($authenticatedToken, $request);

            return null;
        } catch (AuthenticationException $exception) {
            $this->tokenStorage->clear();

            $this->fireFailureLoginEvent($request, $exception);

            return $this->respond->entrypoint($request, $exception);
        }
    }

    protected function createToken(Request $request): GenericLocalToken
    {
        [$identifier, $credentials] = $this->requestMatcher->extractCredentials($request);

        if (!$identifier || !$credentials) {
            throw BadCredentials::invalid();
        }

        return new GenericLocalToken($identifier, $credentials);
    }

    protected function requireAuthentication(Request $request): bool
    {
        try {
            [$identifier, $credentials] = $this->requestMatcher->extractCredentials($request);

            if (!$identifier || !$credentials) {
                return true;
            }

            return !$this->isAlreadyAuthenticated($identifier, $this->tokenStorage->getToken());
        } catch (AuthtersValueFailure $exception) {
            return true;
        }
    }

    protected function isAlreadyAuthenticated(IdentityEmail $identifier, ?Tokenable $token)
    {
        return $token instanceof LocalToken
            && $token->isAuthenticated()
            && $token->getIdentity() instanceof LocalIdentity
            && $token->getIdentity()->getEmail()->sameValueAs($identifier); // set ino contract identity
    }
}