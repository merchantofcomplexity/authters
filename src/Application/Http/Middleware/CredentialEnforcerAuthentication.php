<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Guard\Authentication\Authenticator\EnforcerCredentialAuthenticator;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\GenericLocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request\AuthenticationRequest;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\LocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TrustResolver;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

class CredentialEnforcerAuthentication extends Authentication
{
    /**
     * @var EnforcerCredentialAuthenticator
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

    public function __construct(EnforcerCredentialAuthenticator $authenticator,
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
        // do we have a valid token
        if (!$token = $this->extractFullyAuthenticatedToken()) {
            $exception = new AuthenticationException("Login first");

            return $this->guard->startAuthentication($request, $exception);
        }

        // are we on the enforcer form
        if (!$this->authenticator->isTokenEnforced($token)) {
            if ($this->authenticator->isEnforcerForm($request)) {

                return null; /// endpoint
            }

            if ($this->authenticator->isEnforcerPost($request)) {
                try {
                    $newToken = $this->createAuthenticatedLocalToken($request, $token);

                    $authenticatedToken = $this->authenticator->enforceToken($newToken);

                    // could be avoided
                    $this->guard->storeAuthenticatedToken($authenticatedToken);

                    return response()->redirectToIntended();
                } catch (AuthenticationException $exception) {
                    return $this->authenticator->startAuthentication($request, $exception);
                }
            }

            // on routes to protected
            return $this->authenticator->startAuthentication($request);
        }

        // token enforced

        // nothing to do here if enforcer routes
        if ($this->authenticator->matchEnforcerRoutes($request)) {
            throw new AuthenticationException("Authentication denied");
        }

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