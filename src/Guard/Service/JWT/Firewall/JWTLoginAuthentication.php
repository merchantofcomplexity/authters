<?php

namespace MerchantOfComplexity\Authters\Guard\Service\JWT\Firewall;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Application\Http\Middleware\Authentication;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\GenericLocalToken;
use MerchantOfComplexity\Authters\Guard\HasEventGuard;
use MerchantOfComplexity\Authters\Guard\Service\JWT\Http\Response\JWTResponder;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request\IdentifierCredentialsRequest;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\LocalToken;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use MerchantOfComplexity\Authters\Support\Exception\AuthtersValueFailure;
use MerchantOfComplexity\Authters\Support\Exception\BadCredentials;
use Symfony\Component\HttpFoundation\Response;

final class JWTLoginAuthentication extends Authentication
{
    use HasEventGuard;

    /**
     * @var JWTResponder
     */
    private $responder;

    /**
     * @var IdentifierCredentialsRequest
     */
    private $loginRequest;

    /**
     * @var ContextKey
     */
    private $contextKey;

    public function __construct(JWTResponder $responder,
                                IdentifierCredentialsRequest $loginRequest,
                                ContextKey $contextKey)
    {
        $this->responder = $responder;
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

            return $this->responder->onSuccess($request, $authenticatedToken);
        } catch (AuthenticationException $exception) {
            $this->fireFailureLoginEvent($request, $exception);

            return $this->responder->entryPoint($request, $exception);
        }
    }

    protected function createToken(Request $request): LocalToken
    {
        try {
            [$identifier, $credentials] = $this->loginRequest->extractCredentials($request);
        } catch (AuthtersValueFailure $exception) {
            throw BadCredentials::invalid();
        }

        return new GenericLocalToken($identifier, $credentials, $this->contextKey);
    }

    protected function requireAuthentication(Request $request): bool
    {
        return $this->loginRequest->match($request);
    }
}