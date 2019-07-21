<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Token;

use MerchantOfComplexity\Authters\Firewall\Key\FirewallContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\FirewallKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\LocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Value\Credentials;

final class GenericLocalToken extends Token implements LocalToken
{
    /**
     * @var Credentials
     */
    private $credentials;

    /**
     * @var ContextKey
     */
    private $contextKey;

    public function __construct($user, Credentials $credentials, ContextKey $contextKey, array $roles = [])
    {
        parent::__construct($roles);

        $this->setIdentity($user);

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
        if (is_string($this->contextKey)) {
            $this->contextKey = new FirewallContextKey($this->contextKey);
        }

        return $this->contextKey;
    }

    public function serialize(): string
    {
        return serialize([$this->contextKey->getValue(), parent::serialize()]);
    }

    public function unserialize($serialized)
    {
        [$this->contextKey, $parentStr] = unserialize($serialized, [Tokenable::class]);

        parent::unserialize($parentStr);
    }
}