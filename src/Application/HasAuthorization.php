<?php

namespace MerchantOfComplexity\Authters\Application;

use Illuminate\Contracts\Container\Container;
use LogicException;
use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TrustResolver;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\AuthorizationChecker;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationServiceFailure;
use MerchantOfComplexity\Authters\Support\Exception\AuthorizationDenied;

trait HasAuthorization
{
    /**
     * @var Container
     */
    private $container;

    /**
     * Grant token|identity with permissions|roles and a subject
     *
     * @param string|array $attributes
     * @param object|null $subject
     * @return bool
     */
    protected function isGranted($attributes, object $subject = null): bool
    {
        return $this->authorizationChecker()->isGranted((array)$attributes, $subject);
    }

    /**
     * Grant access or raise an authorization exception
     *
     * @param string|array $attributes
     * @param object|null $subject
     * @return bool
     * @throws AuthorizationDenied
     */
    protected function denyAccessUnlessGranted($attributes, object $subject = null): bool
    {
        if (!$this->isGranted($attributes, $subject)) {
            $this->raiseAuthorizationDenied();
        }

        return true;
    }

    /**
     * Shortcut to get identity from authenticated token
     *
     * @return Identity
     * @throws AuthenticationServiceFailure
     * @throws LogicException
     */
    protected function getIdentity(): Identity
    {
        $token = $this->requireToken();

        if ($this->isFullyAuthenticatedIdentity() || $this->isRememberedIdentity()) {
            return $token->getIdentity();
        }

        throw new LogicException("You must check first if token is not anonymous");
    }

    /**
     * Shortcut to get identifier from authenticated identity
     *
     * @return IdentifierValue
     */
    protected function getIdentityId(): IdentifierValue
    {
        return $this->getIdentity()->getIdentifier();
    }

    /**
     * Check if token is anonymous
     *
     * @return bool
     */
    protected function isAnonymousIdentity(): bool
    {
        return $this->trustResolver()->isAnonymous($this->requireToken());
    }

    /**
     * Check if token is remembered
     *
     * @return bool
     */
    protected function isRememberedIdentity(): bool
    {
        return $this->trustResolver()->isRemembered($this->requireToken());
    }

    /**
     * Check if token is fully authenticated
     *
     * @return bool
     */
    protected function isFullyAuthenticatedIdentity(): bool
    {
        return $this->trustResolver()->isFullyAuthenticated($this->requireToken());
    }

    /**
     * Request token from token storage or raise exception
     *
     * @return Tokenable
     * @throws AuthenticationServiceFailure
     */
    protected function requireToken(): Tokenable
    {
        if ($token = $this->tokenStorage()->getToken()) {
            return $token;
        }

        throw AuthenticationServiceFailure::credentialsNotFound();
    }

    protected function tokenStorage(): TokenStorage
    {
        return $this->container()->get(TokenStorage::class);
    }

    protected function authorizationChecker(): AuthorizationChecker
    {
        return $this->container()->get(AuthorizationChecker::class);
    }

    protected function trustResolver(): TrustResolver
    {
        return $this->container()->get(TrustResolver::class);
    }

    protected function raiseAuthorizationDenied(string $message = null): AuthorizationDenied
    {
        throw AuthorizationDenied::reason($message);
    }

    private function container(): Container
    {
        return $this->container ?? $this->container = app();
    }

    public function setContainer(Container $container)
    {
        $this->container = $container;
    }
}