<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Guard\Authentication\Authenticator\CredentialEnforcerAuthenticator;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\GenericLocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request\AuthenticationRequest;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\LocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TrustResolver;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

class CredentialEnforcerAuthentication extends Authentication
{
    const ROUTE_INTENDED_KEY = 'credential_enforcer_route';

    /**
     * @var CredentialEnforcerAuthenticator
     */
    private $authenticator;

    /**
     * @var ContextKey
     */
    private $contextKey;

    /**
     * @var AuthenticationRequest
     */
    private $enforcerRequest;

    /**
     * @var TrustResolver
     */
    private $trustResolver;

    public function __construct(CredentialEnforcerAuthenticator $authenticator,
                                ContextKey $contextKey,
                                AuthenticationRequest $enforcerRequest,
                                TrustResolver $trustResolver)
    {
        $this->authenticator = $authenticator;
        $this->contextKey = $contextKey;
        $this->enforcerRequest = $enforcerRequest;
        $this->trustResolver = $trustResolver;
    }

    protected function processAuthentication(Request $request): ?Response
    {
        if (!$token = $this->extractFullyAuthenticatedToken()) {
            return $this->guard->startAuthentication($request);
        }

        if (!$this->authenticator->isTokenEnforced($token)) {
            if ($this->authenticator->isEnforcerFormRequest($request)) {
                return null;
            }

            if ($this->authenticator->isEnforcerPostRequest($request)) {
                try {
                    $newToken = $this->createAuthenticatedLocalToken($request, $token);

                    return $this->EnforceAuthenticatedToken($request, $newToken);
                } catch (AuthenticationException $exception) {
                    return $this->authenticator->startAuthentication($request, $exception);
                }
            }

            $this->saveRouteIntended($request);

            return $this->authenticator->startAuthentication($request);
        }

        $this->forgetRouteIntended($request);

        if ($this->authenticator->matchEnforcerRoutes($request)) {
            throw new AuthenticationException("Authentication denied");
        }

        return null;
    }

    protected function EnforceAuthenticatedToken(Request $request, LocalToken $token): Response
    {
        $authenticatedToken = $this->authenticator->enforceToken($token);

        $this->guard->storeAuthenticatedToken($authenticatedToken);

        $response = response()->redirectTo($this->getRouteIntended($request));

        $this->forgetRouteIntended($request);

        return $response;
    }

    protected function createAuthenticatedLocalToken(Request $request, LocalToken $token): LocalToken
    {
        $credentials = $this->enforcerRequest->extractCredentials($request);

        return new GenericLocalToken($token->getIdentity(), $credentials, $this->contextKey);
    }

    protected function extractFullyAuthenticatedToken(): ?LocalToken
    {
        /** @var LocalToken $token */
        $token = $this->guard->storage()->getToken();

        return $this->trustResolver->isFullyAuthenticated($token) ? $token : null;
    }

    protected function requireAuthentication(Request $request): bool
    {
        $token = $this->guard->storage()->getToken();

        return $token instanceof LocalToken
            && (
                $this->enforcerRequest->match($request)
                || $this->authenticator->matchEnforcerRoutes($request)
            );
    }

    protected function forgetRouteIntended(Request $request): void
    {
        $request->session()->forget(self::ROUTE_INTENDED_KEY);
    }

    protected function saveRouteIntended(Request $request)
    {
        $request->session()->put(self::ROUTE_INTENDED_KEY, $request->fullUrl());
    }

    protected function getRouteIntended(Request $request): string
    {
        return $request->session()->get(self::ROUTE_INTENDED_KEY, '/');
    }
}