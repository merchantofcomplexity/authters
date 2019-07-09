<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\Authentication as BaseAuthentication;
use MerchantOfComplexity\Authters\Support\Contract\Domain\RefreshTokenIdentityStrategy;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Events\ContextEvent;
use Symfony\Component\HttpFoundation\Response;
use function unserialize;

final class ContextAuthentication implements BaseAuthentication
{
    /**
     * @var ContextEvent
     */
    private $contextEvent;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var RefreshTokenIdentityStrategy
     */
    private $refreshIdentityStrategy;

    public function __construct(ContextEvent $contextEvent,
                                Dispatcher $dispatcher,
                                TokenStorage $tokenStorage,
                                RefreshTokenIdentityStrategy $refreshIdentityStrategy)
    {
        $this->contextEvent = $contextEvent;
        $this->dispatcher = $dispatcher;
        $this->tokenStorage = $tokenStorage;
        $this->refreshIdentityStrategy = $refreshIdentityStrategy;
    }

    public function handle(Request $request): ?Response
    {
        $this->dispatcher->dispatch($this->contextEvent);

        if ($tokenString = $request->session()->get($this->contextEvent->sessionName())) {
            $this->handleSerializedToken($tokenString);
        }

        return null;
    }

    protected function handleSerializedToken(string $tokenString): void
    {
        $token = unserialize($tokenString, [Tokenable::class]);

        $refreshedToken = $this->refreshIdentityStrategy->refreshTokenIdentity($token);

        $this->tokenStorage->setToken($refreshedToken);
    }
}