<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Firewall\Guard\HasEventGuard;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\GenericLocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\AuthenticationEventGuard;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\StatefulAuthenticationGuard as BaseStatefulMiddleware;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request\IdentifierCredentialsRequest;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Response\AuthenticationResponse;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\LocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Recaller\Recallable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use Symfony\Component\HttpFoundation\Response;

final class LocalAuthentication extends Authentication implements BaseStatefulMiddleware, AuthenticationEventGuard
{
    use HasEventGuard;

    /**
     * @var Recallable
     */
    private $recaller;

    /**
     * @var IdentifierCredentialsRequest
     */
    private $loginRequest;

    /**
     * @var AuthenticationResponse
     */
    private $responder;

    /**
     * @var ContextKey
     */
    private $contextKey;

    public function __construct(IdentifierCredentialsRequest $authenticationRequest,
                                AuthenticationResponse $responder,
                                ContextKey $contextKey)
    {
        $this->loginRequest = $authenticationRequest;
        $this->responder = $responder;
        $this->contextKey = $contextKey;
    }

    protected function processAuthentication(Request $request): ?Response
    {
        $token = $this->createLocalToken($request);

        $this->fireAttemptLoginEvent($request, $token);

        $authenticatedToken = $this->guard->storeAuthenticatedToken($token);

        return $this->onAuthenticationSuccess($request, $authenticatedToken);
    }

    protected function onAuthenticationSuccess(Request $request, Tokenable $token): Response
    {
        $this->fireSuccessLoginEvent($request, $token);

        $response = $this->responder->onSuccess($request, $token);

        if ($this->recaller) {
            $this->recaller->loginSuccess($request, $response, $token);
        }

        return $response;
    }

    protected function createLocalToken(Request $request): LocalToken
    {
        return new GenericLocalToken(
            $this->loginRequest->extractIdentifier($request),
            $this->loginRequest->extractPassword($request),
            $this->contextKey
        );
    }

    protected function requireAuthentication(Request $request): bool
    {
        if ($this->guard->storage()->getToken()) {
            // checkMe to remove to re authenticate a remembered token
            return false;
        }

        return $this->loginRequest->match($request);
    }

    public function setRecaller(Recallable $recaller): void
    {
        $this->recaller = $recaller;
    }
}