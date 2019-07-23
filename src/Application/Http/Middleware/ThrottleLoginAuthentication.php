<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\Authentication as BaseAuthentication;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request\AuthenticationRequest;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request\IdentifierCredentialsRequest;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Events\IdentityLogin;
use MerchantOfComplexity\Authters\Support\Events\IdentityLoginFailed;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use MerchantOfComplexity\Authters\Support\Exception\AuthtersValueFailure;

final class ThrottleLoginAuthentication implements BaseAuthentication
{
    /**
     * @var IdentityLoginFailed|null
     */
    private $loginFailure;

    /**
     * @var IdentityLogin|null
     */
    private $loginSuccess;

    /**
     * @var ContextKey
     */
    private $contextKey;

    /**
     * @var AuthenticationRequest
     */
    private $loginRequest;

    /**
     * @var TokenStorage
     */
    private $storage;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @var RateLimiter
     */
    private $rateLimiter;

    /**
     * @var int
     */
    private $decayMinutes;

    /**
     * @var int
     */
    private $maxAttempts;

    public function __construct(ContextKey $contextKey,
                                IdentifierCredentialsRequest $loginRequest,
                                TokenStorage $storage,
                                Dispatcher $dispatcher,
                                RateLimiter $rateLimiter,
                                ?int $decayMinutes,
                                ?int $maxAttempts)
    {
        $this->contextKey = $contextKey;
        $this->loginRequest = $loginRequest;
        $this->storage = $storage;
        $this->dispatcher = $dispatcher;
        $this->rateLimiter = $rateLimiter;
        $this->decayMinutes = $decayMinutes ?? 1;
        $this->maxAttempts = $maxAttempts ?? 5;
    }

    public function authenticate(Request $request, Closure $next)
    {
        if (!$this->requireAuthentication($request) || !$this->throttleKey($request)) {
            return $next($request);
        }

        $this->dispatcher->listen(IdentityLoginFailed::class, [$this, 'onLoginFailure']);
        $this->dispatcher->listen(IdentityLogin::class, [$this, 'onLoginSuccess']);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->sendLockoutResponse($request);
        }

        $response = $next($request);

        if ($this->loginSuccess) {
            $this->clearLoginAttempts($request);
        }

        if ($this->loginFailure) {
            $this->incrementLoginAttempts($request);

            if ($this->hasTooManyLoginAttempts($request)) {
                $this->sendLockoutResponse($request);
            }
        }

        return $response;
    }

    public function onLoginFailure(IdentityLoginFailed $event): void
    {
        $this->loginFailure = $event;
    }

    public function onLoginSuccess(IdentityLogin $event): void
    {
        $this->loginSuccess = $event;
    }

    protected function requireAuthentication(Request $request): bool
    {
        return !$this->storage->hasToken() && $this->loginRequest->match($request);
    }

    protected function hasTooManyLoginAttempts(Request $request): bool
    {
        return $this->rateLimiter->tooManyAttempts(
            $this->throttleKey($request), $this->maxAttempts
        );
    }

    protected function incrementLoginAttempts(Request $request): void
    {
        $this->rateLimiter->hit(
            $this->throttleKey($request), $this->decayMinutes * 60
        );
    }

    protected function sendLockoutResponse(Request $request): AuthenticationException
    {
        $seconds = $this->rateLimiter->availableIn(
            $this->throttleKey($request)
        );

        throw new AuthenticationException("too many attempts ... retry in $seconds seconds");
    }

    protected function clearLoginAttempts(Request $request): void
    {
        $this->rateLimiter->clear($this->throttleKey($request));
    }

    protected function throttleKey(Request $request): ?string
    {
        try {
            $identifier = $this->loginRequest->extractIdentifier($request);

            return Str::lower(
                $identifier->getValue() .
                '|' . $request->ip() .
                '|' . $this->contextKey->getValue()
            );

        } catch (AuthtersValueFailure $exception) {
            return null;
        }
    }
}