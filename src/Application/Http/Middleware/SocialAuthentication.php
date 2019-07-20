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

    public function __construct(SocialAuthenticator $authenticator, ContextKey $contextKey)
    {
        $this->authenticator = $authenticator;
        $this->contextKey = $contextKey;
    }

    /**
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     * @throws Throwable
     */
    protected function handleAuthenticationFailure(Request $request, Throwable $exception): Response
    {
        $this->guard->clearStorage();

        $authenticationException = $this->handleAuthenticationException($exception);

        if ($authenticationException) {
            $this->fireFailureLoginEvent($request, $authenticationException);

            return $this->guard->startAuthentication($request, $authenticationException);
        }

        throw $exception;
    }

    /**
     * @param Throwable $exception
     * @return AuthenticationException
     * @throws Throwable
     */
    private function handleAuthenticationException(Throwable $exception): ?AuthenticationException
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
            $message = 'Authentication failed';

            return new AuthenticationException($message, 0, $exception);
        }

        return null;
    }
}