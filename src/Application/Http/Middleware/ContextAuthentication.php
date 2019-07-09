<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Domain\RefreshTokenIdentityStrategy;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Events\ContextEvent;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;
use function unserialize;

final class ContextAuthentication extends Authentication
{
    /**
     * @var ContextEvent
     */
    private $contextEvent;

    /**
     * @var RefreshTokenIdentityStrategy
     */
    private $refreshIdentityStrategy;

    public function __construct(ContextEvent $contextEvent,
                                RefreshTokenIdentityStrategy $refreshIdentityStrategy)
    {
        $this->contextEvent = $contextEvent;
        $this->refreshIdentityStrategy = $refreshIdentityStrategy;
    }

    public function processAuthentication(Request $request): ?Response
    {
        try {
            $tokenString = $request->session()->get($this->contextEvent->sessionName());

            $this->handleSerializedToken($tokenString);
        } catch (AuthenticationException $exception) {
            $this->guard->clearStorage();
        } finally {
            return null;
        }
    }

    protected function handleSerializedToken(string $tokenString): void
    {
        $token = unserialize($tokenString, [Tokenable::class]);

        $refreshedToken = $this->refreshIdentityStrategy->refreshTokenIdentity($token);

        $this->guard->storage()->setToken($refreshedToken);
    }

    protected function requireAuthentication(Request $request): bool
    {
        $this->guard->fireAuthenticationEvent($this->contextEvent);

        return $request->session()->has($this->contextEvent->sessionName());
    }
}