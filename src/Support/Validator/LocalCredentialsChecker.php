<?php

namespace MerchantOfComplexity\Authters\Support\Validator;

use MerchantOfComplexity\Authters\Exception\RuntimeException;
use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;
use MerchantOfComplexity\Authters\Support\Contract\Domain\LocalIdentity;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\LocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Validator\CredentialsChecker;
use MerchantOfComplexity\Authters\Support\Contract\Validator\CredentialsValidator;
use MerchantOfComplexity\Authters\Support\Contract\Value\ClearCredentials;
use MerchantOfComplexity\Authters\Support\Contract\Value\EncodedCredentials;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationServiceFailure;
use MerchantOfComplexity\Authters\Support\Exception\BadCredentials;
use MerchantOfComplexity\Authters\Support\Value\Credentials\EmptyCredentials;

class LocalCredentialsChecker implements CredentialsChecker
{
    /**
     * @var CredentialsValidator
     */
    private $credentialsValidator;

    public function __construct(CredentialsValidator $credentialsValidator)
    {
        $this->credentialsValidator = $credentialsValidator;
    }

    public function checkCredentials(Identity $identity, Tokenable $token): void
    {
        if (!$identity instanceof LocalIdentity) {
            throw new AuthenticationServiceFailure("invalid identity given");
        }

        if (!$token instanceof LocalToken) {
            throw new AuthenticationServiceFailure("invalid token given");
        }

        $currentIdentity = $token->getIdentity();
        $tokenCredential = $token->getCredentials();

        if ($currentIdentity instanceof LocalIdentity) {
            if (!$currentIdentity->getPassword()->sameValueAs($identity->getPassword())) {
                throw BadCredentials::hasChanged();
            }

            if (!$tokenCredential instanceof EncodedCredentials) {
                $this->validatePassword($identity->getPassword(), $tokenCredential);
            }
        } else {
            $this->validatePassword($identity->getPassword(), $tokenCredential);
        }
    }

    protected function validatePassword(EncodedCredentials $encodedCredentials, ClearCredentials $clearCredentials): void
    {
        if ($clearCredentials instanceof EmptyCredentials) {
            throw BadCredentials::emptyCredentials();
        }

        if (!is_callable($this->credentialsValidator)) {
            throw new RuntimeException("Credentials Validator must be a callable");
        }

        if (!$this->credentialsValidator->supportsCredentials($encodedCredentials, $clearCredentials)) {
            throw new RuntimeException("Credentials Validator does not support credentials");
        }

        if (!($this->credentialsValidator)($encodedCredentials, $clearCredentials)) {
            throw BadCredentials::invalid();
        }
    }
}