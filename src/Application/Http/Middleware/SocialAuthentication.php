<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Laravel\Socialite\Two\InvalidStateException;
use MerchantOfComplexity\Authters\Firewall\Guard\HasEventGuard;
use MerchantOfComplexity\Authters\Guard\Authentication\Authenticator\SocialAuthenticator;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\AuthenticationEventGuard;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use function get_class;

abstract class SocialAuthentication extends Authentication implements AuthenticationEventGuard
{
    use HasEventGuard;

    /**
     * @var SocialAuthenticator
     */
    protected $authenticator;

    /**
     * @var ContextKey
     */
    protected $contextKey;

    public function __construct(SocialAuthenticator $authenticator,ContextKey $contextKey)
    {
        $this->authenticator = $authenticator;
        $this->contextKey = $contextKey;
    }

    protected function onException(Request $request, Throwable $exception): Response
    {
        $this->guard->clearStorage();

        return $this->guard->startAuthentication($request, $this->handleException($exception));
    }

    private function handleException(Throwable $exception): AuthenticationException
    {
        if ($exception instanceof AuthenticationException) {
            return $exception;
        }

        $handled = [
            InvalidStateException::class,
            InvalidArgumentException::class,
            ClientException::class
        ];

        if (in_array(get_class($exception), $handled)) {
            $exception = new AuthenticationException(
                "Authentication failed", 0, $exception
            );

            return $exception;
        }

        throw $exception;
    }
}