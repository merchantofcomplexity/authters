<?php

namespace MerchantOfComplexity\Authters\Firewall\Factory;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Events\IdentityAttemptLogin;
use MerchantOfComplexity\Authters\Support\Events\IdentityLogin;
use MerchantOfComplexity\Authters\Support\Events\IdentityLoginFailed;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;

trait HasEventGuard
{
    public function fireAttemptLoginEvent(Request $request, Tokenable $token): void
    {
        $this->guard->fireAuthenticationEvent(new IdentityAttemptLogin($request, $token));
    }

    public function fireSuccessLoginEvent(Request $request, Tokenable $token): void
    {
        $this->guard->fireAuthenticationEvent(new IdentityLogin($request, $token));

    }

    public function fireFailureLoginEvent(Request $request, AuthenticationException $exception): void
    {
        $this->guard->fireAuthenticationEvent(new IdentityLoginFailed($request, $exception));
    }
}