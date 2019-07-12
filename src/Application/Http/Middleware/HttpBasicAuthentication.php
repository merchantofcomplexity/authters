<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Firewall\Guard\HasEventGuard;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\GenericLocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\AuthenticationEventGuard;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request\AuthenticationRequest;
use MerchantOfComplexity\Authters\Support\Contract\Domain\LocalIdentity;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\LocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentityEmail;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use MerchantOfComplexity\Authters\Support\Exception\AuthtersValueFailure;
use MerchantOfComplexity\Authters\Support\Exception\BadCredentials;
use Symfony\Component\HttpFoundation\Response;

final class HttpBasicAuthentication extends Authentication implements AuthenticationEventGuard
{
    use HasEventGuard;

    /**
     * @var AuthenticationRequest
     */
    private $authenticationRequest;

    /**
     * @var ContextKey
     */
    private $contextKey;

    public function __construct(AuthenticationRequest $authenticationRequest, ContextKey $contextKey)
    {
        $this->authenticationRequest = $authenticationRequest;
        $this->contextKey = $contextKey;
    }

    protected function processAuthentication(Request $request): ?Response
    {
        try {
            $token = $this->createToken($request);

            $this->fireAttemptLoginEvent($request, $token);

            $authenticatedToken = $this->guard->storeAuthenticatedToken($token);

            $this->fireSuccessLoginEvent($request, $authenticatedToken);

            return null;
        } catch (AuthenticationException $exception) {
            $this->guard->clearStorage();

            $this->fireFailureLoginEvent($request, $exception);

            return $this->guard->startAuthentication($request, $exception);
        }
    }

    protected function createToken(Request $request): GenericLocalToken
    {
        [$identifier, $credentials] = $this->authenticationRequest->extractCredentials($request);

        if (!$identifier || !$credentials) {
            throw BadCredentials::invalid();
        }

        return new GenericLocalToken($identifier, $credentials, $this->contextKey);
    }

    protected function requireAuthentication(Request $request): bool
    {
        try {
            [$identifier, $credentials] = $this->authenticationRequest->extractCredentials($request);

            if (!$identifier || !$credentials) {
                return true;
            }

            return !$this->isAlreadyAuthenticated($identifier, $this->guard->storage()->getToken());
        } catch (AuthtersValueFailure $exception) {
            return true;
        }
    }

    protected function isAlreadyAuthenticated(IdentityEmail $identifier, ?Tokenable $token)
    {
        return $token instanceof LocalToken
            && $token->isAuthenticated()
            && $token->getIdentity() instanceof LocalIdentity
            && $token->getIdentity()->getEmail()->sameValueAs($identifier); // fixMe
    }
}