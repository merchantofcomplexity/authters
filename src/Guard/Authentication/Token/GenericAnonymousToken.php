<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Token;

use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\AnonymousKey;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\FirewallKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\AnonymousToken;
use MerchantOfComplexity\Authters\Support\Contract\Value\Credentials;
use MerchantOfComplexity\Authters\Support\Value\Credentials\EmptyCredentials;
use MerchantOfComplexity\Authters\Support\Value\Identifier\AnonymousIdentifier;

final class GenericAnonymousToken extends Token implements AnonymousToken
{
    /**
     * @var AnonymousKey
     */
    private $anonymousKey;

    public function __construct(AnonymousKey $anonymousKey)
    {
        parent::__construct();

        $this->setIdentity(new AnonymousIdentifier());

        $this->anonymousKey = $anonymousKey;

        $this->setAuthenticated(true);
    }

    public function getCredentials(): Credentials
    {
        return new EmptyCredentials();
    }

    public function getFirewallKey(): FirewallKey
    {
        return $this->anonymousKey;
    }
}