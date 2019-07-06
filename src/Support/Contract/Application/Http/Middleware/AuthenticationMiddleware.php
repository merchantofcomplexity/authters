<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware;

use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request\AuthenticationRequest;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Response\AuthenticationResponse;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Authenticatable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;

interface AuthenticationMiddleware
{
    public function setAuthenticationManager(Authenticatable $manager): void;

    public function setTokenStorage(TokenStorage $tokenStorage): void;

    public function setResponder(AuthenticationResponse $responder): void;

    public function setRequestMatcher(AuthenticationRequest $requestMatcher): void;

}