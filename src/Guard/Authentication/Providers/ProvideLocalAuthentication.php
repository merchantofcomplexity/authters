<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Providers;

use MerchantOfComplexity\Authters\Domain\Role\SwitchIdentityRole;
use MerchantOfComplexity\Authters\Exception\RuntimeException;
use MerchantOfComplexity\Authters\Support\Contract\Domain\IdentityChecker;
use MerchantOfComplexity\Authters\Support\Contract\Domain\IdentityProvider;
use MerchantOfComplexity\Authters\Support\Contract\Domain\LocalIdentity;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\AuthenticationProvider;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\LocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Validator\CredentialsChecker;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationServiceFailure;
use MerchantOfComplexity\Authters\Support\Exception\BadCredentials;
use MerchantOfComplexity\Authters\Support\Exception\IdentityNotFound;

abstract class ProvideLocalAuthentication implements AuthenticationProvider
{
    /**
     * @var IdentityProvider
     */
    private $userProvider;

    /**
     * @var IdentityChecker
     */
    private $identityChecker;

    /**
     * @var CredentialsChecker
     */
    private $credentialsChecker;

    /**
     * @var ContextKey
     */
    private $contextKey;

    public function __construct(IdentityProvider $userProvider,
                                IdentityChecker $identityChecker,
                                CredentialsChecker $credentialsChecker,
                                ContextKey $contextKey)
    {
        $this->userProvider = $userProvider;
        $this->identityChecker = $identityChecker;
        $this->credentialsChecker = $credentialsChecker;
        $this->contextKey = $contextKey;
    }

    public function authenticate(Tokenable $token): Tokenable
    {
        if (!$token instanceof LocalToken) {
            throw new RuntimeException("Local authentication provider supports local token only");
        }

        $user = $this->retrieveUser($token);

        try {
            $this->checkIdentity($user, $token);
        } catch (BadCredentials $badCredentials) {
            throw IdentityNotFound::hideBadCredentials($badCredentials);
        }

        return $this->createAuthenticatedToken($user, $token, $this->contextKey);
    }

    abstract protected function createAuthenticatedToken(LocalIdentity $user,
                                                         LocalToken $token,
                                                         ContextKey $contextKey): Tokenable;

    private function retrieveUser(Tokenable $token): LocalIdentity
    {
        $identity = $token->getIdentity();

        if ($identity instanceof LocalIdentity) {
            return $identity;
        }

        $identity = $this->userProvider->requireIdentityOfIdentifier($identity);

        if (!$identity instanceof LocalIdentity) {
            throw new AuthenticationServiceFailure(
                "Identity provider must return an implementation of " . LocalIdentity::class
            );
        }

        return $identity;
    }

    private function checkIdentity(LocalIdentity $identity, LocalToken $token): void
    {
        $this->identityChecker->onPreAuthentication($identity);

        $this->credentialsChecker->checkCredentials($identity, $token);

        $this->identityChecker->onPostAuthentication($identity);
    }

    protected function mergeDynamicRoles(LocalIdentity $identity, Tokenable $token): array
    {
        $roles = $identity->getRoles();

        foreach ($token->getRoles() as $role) {
            if ($role instanceof SwitchIdentityRole) {
                $roles[] = $role;
            }
        }

        return $roles;
    }

    public function supportToken(Tokenable $token): bool
    {
        return $token instanceof LocalToken
            && $token->getFirewallKey()->sameValueAs($this->contextKey);
    }
}