<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\GenericLocalToken;
use MerchantOfComplexity\Authters\Guard\HasEventGuard;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\AuthenticationEventGuard;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request\IdentifierCredentialsRequest;
use MerchantOfComplexity\Authters\Support\Contract\Domain\LocalIdentity;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\LocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use MerchantOfComplexity\Authters\Support\Exception\AuthtersValueFailure;
use Symfony\Component\HttpFoundation\Response;

final class HttpBasicAuthentication extends Authentication implements AuthenticationEventGuard
{
    use HasEventGuard;

    /**
     * @var IdentifierCredentialsRequest
     */
    private $loginRequest;

    /**
     * @var ContextKey
     */
    private $contextKey;

    public function __construct(IdentifierCredentialsRequest $loginRequest, ContextKey $contextKey)
    {
        $this->loginRequest = $loginRequest;
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
        return new GenericLocalToken(
            $this->loginRequest->extractIdentifier($request),
            $this->loginRequest->extractPassword($request),
            $this->contextKey
        );
    }

    protected function requireAuthentication(Request $request): bool
    {
        try {
            return $this->isNotAlreadyAuthenticated(
                $this->loginRequest->extractIdentifier($request),
                $this->guard->storage()->getToken()
            );
        } catch (AuthtersValueFailure $exception) {
            return true;
        }
    }

    protected function isNotAlreadyAuthenticated(IdentifierValue $identifier, ?Tokenable $token)
    {
        return !(
            $token instanceof LocalToken
            && $token->isAuthenticated()
            && $token->getIdentity() instanceof LocalIdentity
            && $token->getIdentity()->getIdentifier()->sameValueAs($identifier));
    }
}