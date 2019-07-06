<?php

namespace MerchantOfComplexity\Authters\Guard\Authorization;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Authenticatable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\AuthorizationChecker as BaseAuthorizationChecker;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\AuthorizationStrategy;

final class AuthorizationChecker implements BaseAuthorizationChecker
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Authenticatable
     */
    private $authenticationManager;

    /**
     * @var AuthorizationStrategy
     */
    private $authorizationStrategy;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var bool
     */
    private $alwaysAuthenticate;

    public function __construct(Authenticatable $authenticationManager,
                                AuthorizationStrategy $authorizationStrategy,
                                TokenStorage $tokenStorage,
                                bool $alwaysAuthenticate = false)
    {
        $this->authenticationManager = $authenticationManager;
        $this->authorizationStrategy = $authorizationStrategy;
        $this->tokenStorage = $tokenStorage;
        $this->alwaysAuthenticate = $alwaysAuthenticate;
    }

    public function isGranted(Tokenable $token, array $attributes, object $subject = null): bool
    {
        $token = $this->authenticateToken($token);

        return $this->authorizationStrategy->decide($token, $attributes, $subject ?? $this->request);
    }

    protected function authenticateToken(Tokenable $token): Tokenable
    {
        if ($this->alwaysAuthenticate || !$token->isAuthenticated()) {
            $token = $this->authenticationManager->authenticate($token);

            $this->tokenStorage->setToken($token);
        }

        return $token;
    }

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }
}