<?php

namespace MerchantOfComplexity\Authters\Exception;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Response\AccessDenied;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Response\Entrypoint;
use MerchantOfComplexity\Authters\Support\Contract\Exception\FirewallException;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TrustResolver;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use MerchantOfComplexity\Authters\Support\Exception\AuthorizationException;
use MerchantOfComplexity\Authters\Support\Exception\IdentityStatusException;
use MerchantOfComplexity\Authters\Support\Exception\InsufficientAuthentication;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class FirewallExceptionHandler
{
    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var TrustResolver
     */
    private $trustResolver;

    /**
     * @var ContextKey
     */
    private $contextKey;

    /**
     * @var bool
     */
    private $stateless;

    /**
     * @var Entrypoint|null
     */
    private $entrypoint;

    /**
     * @var AccessDenied|null
     */
    private $accessDenied;

    public function __construct(TokenStorage $tokenStorage,
                                TrustResolver $trustResolver,
                                ContextKey $contextKey,
                                bool $stateless,
                                ?Entrypoint $entrypoint,
                                ?AccessDenied $accessDenied)
    {
        $this->tokenStorage = $tokenStorage;
        $this->trustResolver = $trustResolver;
        $this->contextKey = $contextKey;
        $this->stateless = $stateless;
        $this->entrypoint = $entrypoint;
        $this->accessDenied = $accessDenied;
    }

    /**
     * @param Request $request
     * @param FirewallException $exception
     * @return Response
     * @throws FirewallException
     */
    public function handle(Request $request, FirewallException $exception): Response
    {
        if ($exception instanceof AuthenticationException) {
            return $this->onAuthenticationException($request, $exception);
        } elseif ($exception instanceof AuthorizationException) {
            return $this->onAuthorizationException($request, $exception);
        } elseif ($exception instanceof HttpException) {
            throw $exception; // fixMe
        }

        // todo logout

        throw $exception;
    }

    protected function onAuthenticationException(Request $request, AuthenticationException $exception): Response
    {
        // todo handle Auth service failure

        return $this->startAuthentication($request, $exception);
    }

    protected function onAuthorizationException(Request $request, AuthorizationException $exception): Response
    {
        $token = $this->tokenStorage->getToken();

        if (!$this->trustResolver->isFullyAuthenticated($token)) {
            return $this->whenIdentityIsNotFullyAuthenticated($request, $exception);
        }

        return $this->whenIdentityIsNotGranted($request, $exception);
    }

    protected function whenIdentityIsNotGranted(Request $request, AuthorizationException $exception): Response
    {
        if (!$this->accessDenied) {
            throw $exception;
        }

        return $this->accessDenied->onAuthorizationDenied($request, $exception);
    }

    protected function whenIdentityIsNotFullyAuthenticated(Request $request, AuthorizationException $exception): Response
    {
        $message = "Full authentication is required to access this resource";

        $authenticationException = new InsufficientAuthentication($message, 0, $exception);

        return $this->startAuthentication($request, $authenticationException);
    }

    protected function startAuthentication(Request $request, AuthenticationException $exception): Response
    {
        if (!$this->entrypoint) {
            throw $exception;
        }

        if ($exception instanceof IdentityStatusException) {
            $this->tokenStorage->clear();
        }

        return $this->entrypoint->startAuthentication($request, $exception);
    }
}