<?php

namespace MerchantOfComplexity\Authters\Application;

use Illuminate\Contracts\Container\Container;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TrustResolver;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\AuthorizationChecker;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationServiceFailure;
use MerchantOfComplexity\Authters\Support\Exception\AuthorizationDenied;

trait HasAuthorization
{
    /**
     * @var Container
     */
    protected $container;

    protected function denyAccessUnlessGranted(array $attributes, object $subject = null): bool
    {
        return $this->authorizationChecker()->isGranted(
            $this->requireToken(),
            $attributes,
            $subject ?? request()
        );
    }

    /**
     * @param array $attributes
     * @param object|null $subject
     * @return bool
     * @throws AuthorizationDenied
     */
    protected function requireAccessUnlessGranted(array $attributes, object $subject = null): bool
    {
        if (!$this->denyAccessUnlessGranted($attributes, $subject)) {
            $this->raiseAuthorizationDenied();
        }

        return true;
    }

    protected function isAnonymousIdentity(): bool
    {
        return $this->trustResolver()->isAnonymous($this->requireToken());
    }

    protected function isRememberedIdentity(): bool
    {
        return $this->trustResolver()->isRemembered($this->requireToken());
    }

    protected function isFullyAuthenticatedIdentity(): bool
    {
        return $this->trustResolver()->isFullyAuthenticated($this->requireToken());
    }

    /**
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
        return $this->container->get(TrustResolver::class);
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