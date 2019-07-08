<?php

namespace MerchantOfComplexity\Authters\Support\Middleware;

use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request\AuthenticationRequest;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Response\AuthenticationResponse;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Authenticatable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;

trait HasAuthentication
{
    /**
     * @var Authenticatable
     */
    private $authenticationManager;

    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * @var AuthenticationResponse
     */
    protected $respond;

    /**
     * @var AuthenticationRequest
     */
    protected $requestMatcher;

    public function setAuthenticationManager(Authenticatable $authenticationManager): void
    {
        $this->authenticationManager = $authenticationManager;
    }

    public function setTokenStorage(TokenStorage $tokenStorage): void
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function setResponder(AuthenticationResponse $respond): void
    {
        $this->respond = $respond;
    }

    public function setRequestMatcher(AuthenticationRequest $requestMatcher): void
    {
        $this->requestMatcher = $requestMatcher;
    }

    protected function storeAuthenticatedToken(Tokenable $token): Tokenable
    {
        $authenticatedToken = $this->authenticationManager->authenticate($token);

        $this->tokenStorage->setToken($authenticatedToken);

        return $authenticatedToken;
    }
}