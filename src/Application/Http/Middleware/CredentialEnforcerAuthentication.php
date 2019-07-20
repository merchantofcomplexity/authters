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
            $exception = new AuthenticationException("Login first");

            return $this->guard->startAuthentication($request, $exception);
        }

        if (!$this->authenticator->isTokenEnforced($token)) {
            if ($this->authenticator->isEnforcerForm($request)) {
                return null;
            }

            if ($this->authenticator->isEnforcerPost($request)) {
                try {
                    $newToken = $this->createAuthenticatedLocalToken($request, $token);

                    $authenticatedToken = $this->authenticator->enforceToken($newToken);

                    $this->guard->storeAuthenticatedToken($authenticatedToken);

                    $response = response()->redirectTo($request->session()->get(self::ROUTE_INTENDED_KEY));

                    $request->session()->forget(self::ROUTE_INTENDED_KEY);

                    return $response;
                } catch (AuthenticationException $exception) {
                    return $this->authenticator->startAuthentication($request, $exception);
                }
            }

            $request->session()->put(self::ROUTE_INTENDED_KEY, $request->fullUrl());

            return $this->authenticator->startAuthentication($request);
        }

        if ($this->authenticator->matchEnforcerRoutes($request)) {
            throw new AuthenticationException("Authentication denied");
        }

        $request->session()->forget(self::ROUTE_INTENDED_KEY);

        return null;
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

        if ($this->trustResolver->isFullyAuthenticated($token)) {
            return $token;
        }

        return null;
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
}