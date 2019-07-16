<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Token;

use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\FirewallKey;
use MerchantOfComplexity\Authters\Support\Contract\Value\Credentials;

final class SocialToken extends Token
{
    /**
     * @var Credentials
     */
    private $credentials;

    /**
     * @var ContextKey
     */
    private $contextKey;

    public function __construct($identity, Credentials $credentials, ContextKey $contextKey, array $roles = [])
    {
        parent::__construct($roles);

        $this->setIdentity($identity);
        $this->credentials = $credentials;
        $this->contextKey = $contextKey;

        $this->hasRoles() and $this->setAuthenticated(true);
    }

    public function getCredentials(): Credentials
    {
        return $this->credentials;
    }

    public function getFirewallKey(): FirewallKey
    {
        return $this->contextKey;
    }
}