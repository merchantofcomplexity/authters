<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Token;

use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\AnonymousToken;
use MerchantOfComplexity\Authters\Support\Contract\Value\Credentials;
use MerchantOfComplexity\Authters\Support\Value\Credentials\EmptyCredentials;
use MerchantOfComplexity\Authters\Support\Value\Identifier\AnonymousIdentifier;

final class GenericAnonymousToken extends Token implements AnonymousToken
{
    public function __construct()
    {
        parent::__construct();

        $this->setIdentity(new AnonymousIdentifier());

        $this->setAuthenticated(true);
    }

    public function getCredentials(): Credentials
    {
        return new EmptyCredentials();
    }
}