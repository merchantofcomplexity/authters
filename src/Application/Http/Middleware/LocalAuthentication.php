<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Firewall\Factory\HasEventGuard;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\GenericLocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\StatefulAuthenticationGuard as BaseStatefulMiddleware;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request\AuthenticationRequest;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Response\AuthenticationResponse;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Exception\BadCredentials;
use Symfony\Component\HttpFoundation\Response;

final class LocalAuthentication extends Authentication implements BaseStatefulMiddleware
{
    use HasEventGuard;

    /**
     * @var $recaller
     */
    private $recaller;

    /**
     * @var AuthenticationRequest
     */
    private $authenticationRequest;

    /**
     * @var AuthenticationResponse
     */
    private $responder;

    /**
     * @var ContextKey
     */
    private $contextKey;

    public function __construct(AuthenticationRequest $authenticationRequest,
                                AuthenticationResponse $responder,
                                ContextKey $contextKey)
    {
        $this->authenticationRequest = $authenticationRequest;
        $this->responder = $responder;
        $this->contextKey = $contextKey;
    }

    protected function processAuthentication(Request $request): ?Response
    {
        [$email, $password] = $this->extractCredentials($request);

        $token = new GenericLocalToken($email, $password, $this->contextKey);

        $this->fireAttemptLoginEvent($request, $token);

        $authenticatedToken = $this->guard->storeAuthenticatedToken($token);

        return $this->onAuthenticationSuccess($request, $authenticatedToken);
    }

    protected function onAuthenticationSuccess(Request $request, Tokenable $token): Response
    {
        $this->fireSuccessLoginEvent($request, $token);

        $response = $this->responder->onSuccess($request, $token);

        if ($this->recaller) {
            //
        }

        return $response;
    }

    protected function extractCredentials(Request $request): array
    {
        [$identifier, $credentials] = $this->authenticationRequest->extractCredentials($request);

        if (!$identifier || !$credentials) {
            throw BadCredentials::invalid();
        }

        return [$identifier, $credentials];
    }

    protected function requireAuthentication(Request $request): bool
    {
        if ($this->guard->storage()->getToken()) {
            return false;
        }

        return $this->authenticationRequest->match($request);
    }

    public function setRecaller($recaller): void
    {
        $this->recaller = $recaller;
    }
}