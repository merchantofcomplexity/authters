<?php

namespace MerchantOfComplexity\Authters\Guard\Service\JWT\Firewall;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Application\Http\Middleware\Authentication;
use MerchantOfComplexity\Authters\Guard\Service\JWT\Http\Request\BearerRequest;
use MerchantOfComplexity\Authters\Guard\Service\JWT\Http\Response\JWTResponder;
use MerchantOfComplexity\Authters\Guard\Service\JWT\JWTToken;
use MerchantOfComplexity\Authters\Guard\Service\JWT\Value\JWTAnonymousIdentifier;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

final class JWTAuthentication extends Authentication
{
    /**
     * @var JWTResponder
     */
    private $responder;

    /**
     * @var BearerRequest
     */
    private $bearerRequest;

    /**
     * @var ContextKey
     */
    private $contextKey;

    public function __construct(JWTResponder $responder, BearerRequest $bearerRequest, ContextKey $contextKey)
    {
        $this->responder = $responder;
        $this->bearerRequest = $bearerRequest;
        $this->contextKey = $contextKey;
    }

    protected function processAuthentication(Request $request): ?Response
    {
        if (!$request->bearerToken()) {
            $authenticationException = new AuthenticationException("Missing bearer token");

            return $this->responder->entryPoint($request, $authenticationException);
        }

        try {
            $token = $this->createToken($request);

            $this->guard->storeAuthenticatedToken($token);

            return null;
        } catch (AuthenticationException $exception) {
            return $this->responder->onFailure($request, $exception);
        }
    }

    protected function createToken(Request $request): JWTToken
    {
        $credentials = $this->bearerRequest->extractCredentials($request);

        return new JWTToken(new JWTAnonymousIdentifier(), $credentials, $this->contextKey);
    }

    protected function requireAuthentication(Request $request): bool
    {
        return $this->bearerRequest->match($request);
    }
}