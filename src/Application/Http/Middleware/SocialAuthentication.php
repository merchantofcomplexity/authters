<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Laravel\Socialite\Two\InvalidStateException;
use MerchantOfComplexity\Authters\Application\Http\Response\SocialProviderEntrypoint;
use MerchantOfComplexity\Authters\Guard\Authentication\Authenticator\SocialAuthenticator;
use MerchantOfComplexity\Authters\Guard\HasEventGuard;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\AuthenticationEventGuard;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use MerchantOfComplexity\Authters\Support\Exception\IdentityNotFound;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use function get_class;

final class SocialAuthentication extends Authentication implements AuthenticationEventGuard
{
    use HasEventGuard;

    /**
     * @var SocialAuthenticator
     */
    private $authenticator;

    /**
     * @var SocialProviderEntrypoint
     */
    private $entrypoint;

    /**
     * @var ContextKey
     */
    private $contextKey;

    public function __construct(SocialAuthenticator $authenticator,
                                SocialProviderEntrypoint $entrypoint,
                                ContextKey $contextKey)
    {
        $this->authenticator = $authenticator;
        $this->entrypoint = $entrypoint;
        $this->contextKey = $contextKey;
    }

    /**
     * @param Request $request
     * @return Response|null
     * @throws Throwable
     */
    protected function processAuthentication(Request $request): ?Response
    {
        if ($this->authenticator->socialRequest()->isRedirect($request)) {
            return $this->handleRedirectRequest($request);
        }

        return $this->handleLoginRequest($request);
    }

    /**
     * @param Request $request
     * @return Response|null
     * @throws Throwable
     */
    protected function handleRedirectRequest(Request $request): ?Response
    {
        try {
            // first we create a token with a "need registration" role
            // if we succeed to authenticate the token, identity has been already registered
            // we store a new token with a "login" role
            // we keep workflow in both case to be handled in a endpoint
            $token = $this->authenticator->createRegistrationSocialToken($request, $this->contextKey);

            $this->fireAttemptLoginEvent($request, $token);

            try {
                $token = $this->authenticator->createLoginSocialToken(
                    $this->guard->authenticateToken($token)
                );
            } catch (IdentityNotFound $notFound) {
                //
            }

            $this->fireSuccessLoginEvent($request, $token);

            $this->guard->storage()->setToken($token);

            return null;
        } catch (Throwable $exception) {
            return $this->handleAuthenticationFailure($request, $exception);
        }
    }

    protected function handleLoginRequest(Request $request): Response
    {
        try {
            $this->authenticator->extractProviderName($request);

            return $this->entrypoint->startAuthentication($request);
        } catch (Throwable $exception) {
            return $this->handleAuthenticationFailure($request, $exception);
        }
    }

    protected function requireAuthentication(Request $request): bool
    {
        return $this->guard->isStorageEmpty()
            && $this->authenticator->socialRequest()->match($request);
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

    protected function handleAuthenticationException(Throwable $exception): ?AuthenticationException
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