<?php

namespace MerchantOfComplexity\Authters\Support\Middleware;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Events\IdentityAttemptLogin;
use MerchantOfComplexity\Authters\Support\Events\IdentityLogin;
use MerchantOfComplexity\Authters\Support\Events\IdentityLoginFailed;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;

trait HasAuthenticationEvent
{
    /**
     * @var Dispatcher $dispatcher
     */
    protected $dispatcher;

    public function setDispatcher(Dispatcher $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }

    public function fireSuccessLoginEvent(Tokenable $token, Request $request): void
    {
        $this->dispatcher->dispatch(new IdentityLogin($request, $token));
    }

    public function fireFailureLoginEvent(Request $request, AuthenticationException $exception): void
    {
        $this->dispatcher->dispatch(new IdentityLoginFailed($request, $exception));
    }

    public function fireAttemptLoginEvent(Request $request, Tokenable $token): void
    {
        $this->dispatcher->dispatch(new IdentityAttemptLogin($request, $token));
    }
}