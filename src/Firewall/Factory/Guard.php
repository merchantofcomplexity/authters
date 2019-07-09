<?php

namespace MerchantOfComplexity\Authters\Firewall\Factory;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Response\Entrypoint;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Guardable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Authenticatable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

class Guard implements Guardable
{
    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var Authenticatable
     */
    private $authenticationManager;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @var Entrypoint
     */
    private $entrypoint;

    public function __construct(TokenStorage $tokenStorage,
                                Authenticatable $authenticationManager,
                                Dispatcher $dispatcher,
                                Entrypoint $entrypoint)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
        $this->dispatcher = $dispatcher;
        $this->entrypoint = $entrypoint;
    }

    public function authenticateToken(Tokenable $token): Tokenable
    {
        return $this->authenticationManager->authenticate($token);
    }

    public function storeAuthenticatedToken(Tokenable $token): Tokenable
    {
        $this->tokenStorage->setToken(
            $authenticatedToken = $this->authenticateToken($token)
        );

        return $authenticatedToken;
    }

    public function startAuthentication(Request $request, AuthenticationException $exception): Response
    {
        return $this->entrypoint->startAuthentication($request, $exception);
    }

    public function fireAuthenticationEvent($authenticationEvent, array $payload = [], bool $halt = false)
    {
        return $this->dispatcher->dispatch($authenticationEvent, $payload, $halt);
    }

    public function isStorageEmpty(): bool
    {
        return null === $this->storage()->getToken();
    }

    public function isStorageFilled(): bool
    {
        return null !== $this->storage()->getToken();
    }

    public function clearStorage(): void
    {
        $this->tokenStorage->clear();
    }

    public function storage(): TokenStorage
    {
        return $this->tokenStorage;
    }
}