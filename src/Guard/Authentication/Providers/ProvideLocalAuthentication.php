<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Providers;

use MerchantOfComplexity\Authters\Exception\RuntimeException;
use MerchantOfComplexity\Authters\Support\Contract\Domain\IdentityChecker;
use MerchantOfComplexity\Authters\Support\Contract\Domain\IdentityProvider;
use MerchantOfComplexity\Authters\Support\Contract\Domain\LocalIdentity;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\AuthenticationProvider;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\LocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Validator\CredentialsValidator;
use MerchantOfComplexity\Authters\Support\Contract\Value\ClearCredentials;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationServiceFailure;
use MerchantOfComplexity\Authters\Support\Exception\BadCredentials;
use MerchantOfComplexity\Authters\Support\Exception\IdentityNotFound;
use MerchantOfComplexity\Authters\Support\Value\Credentials\EmptyCredentials;

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
     * @var CredentialsValidator
     */
    private $credentialsValidator;

    /**
     * @var ContextKey
     */
    private $contextKey;

    public function __construct(IdentityProvider $userProvider,
                                IdentityChecker $identityChecker,
                                CredentialsValidator $credentialsValidator,
                                ContextKey $contextKey)
    {
        $this->userProvider = $userProvider;
        $this->identityChecker = $identityChecker;
        $this->credentialsValidator = $credentialsValidator;
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
                                                         Tokenable $token,
                                                         ContextKey $contextKey): Tokenable;

    private function retrieveUser(Tokenable $token): LocalIdentity
    {
        $identity = $token->getIdentity();

        if ($identity instanceof LocalIdentity) {
            return $identity;
        }

        $identity = $this->userProvider->requireIdentityOfIdentifier($identity);

        if (!$identity instanceof LocalIdentity) {
            $exceptionMessage = "Identity provider must return an implementation of " . LocalIdentity::class;

            throw new AuthenticationServiceFailure($exceptionMessage);
        }

        return $identity;
    }

    private function checkIdentity(LocalIdentity $identity, LocalToken $token): void
    {
        $this->identityChecker->onPreAuthentication($identity);

        $this->checkCredentials($identity, $token);

        $this->identityChecker->onPostAuthentication($identity);
    }

    // checkMe abstract this bloc
    private function checkCredentials(LocalIdentity $identity, LocalToken $token): void
    {
        $currentIdentity = $token->getIdentity();

        if ($currentIdentity instanceof LocalIdentity) {
            if (!$currentIdentity->getPassword()->sameValueAs($identity->getPassword())) {
                throw BadCredentials::hasChanged();
            }
        } else {
            /** @var ClearCredentials $presentedPassword */
            $presentedPassword = $token->getCredentials();

            if ($presentedPassword instanceof EmptyCredentials) {
                throw BadCredentials::emptyCredentials();
            }

            if (!is_callable($this->credentialsValidator)) {
                throw new RuntimeException("Credentials Validator must be a callable");
            }

            if (!$this->credentialsValidator->supportsCredentials($identity->getPassword(), $presentedPassword)) {
                throw new RuntimeException("Credentials Validator does not support credentials");
            }

            if (!($this->credentialsValidator)($identity->getPassword(), $presentedPassword)) {
                throw BadCredentials::invalid();
            }
        }
    }

    public function supportToken(Tokenable $token): bool
    {
        return $token instanceof LocalToken
            && $token->getFirewallKey()->sameValueAs($this->contextKey);
    }
}