<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;

interface AuthenticationEventGuard extends AuthenticationGuard
{
    public function fireAttemptLoginEvent(Request $request, Tokenable $token): void;

    public function fireSuccessLoginEvent(Tokenable $token, Request $request): void;

    public function fireFailureLoginEvent(Request $request, AuthenticationException $exception): void;
}