<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request\AuthenticationRequest;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Response\AuthenticationResponse;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Authenticatable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;

interface AuthenticationAware extends Authentication
{
    public function setAuthenticationManager(Authenticatable $manager): void;

    public function setTokenStorage(TokenStorage $tokenStorage): void;

    public function setResponder(AuthenticationResponse $responder): void;

    public function setRequestMatcher(AuthenticationRequest $requestMatcher): void;
}