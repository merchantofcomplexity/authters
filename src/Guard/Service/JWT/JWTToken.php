<?php

namespace MerchantOfComplexity\Authters\Guard\Service\JWT;

use MerchantOfComplexity\Authters\Guard\Authentication\Token\Token;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\FirewallKey;
use MerchantOfComplexity\Authters\Support\Contract\Value\Credentials;

final class JWTToken extends Token
{
    /**
     * @var Credentials
     */
    private $credentials;

    /**
     * @var FirewallKey
     */
    private $key;

    public function __construct($identity, Credentials $credentials, FirewallKey $key, array $roles = [])
    {
        parent::__construct($roles);
        $this->setIdentity($identity);

        $this->credentials = $credentials;
        $this->key = $key;

        $this->hasRoles() and $this->setAuthenticated(true);
    }

    public function getCredentials(): Credentials
    {
        return $this->credentials;
    }

    public function getFirewallKey(): FirewallKey
    {
        return $this->key;
    }
}