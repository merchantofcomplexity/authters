<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Token;

use MerchantOfComplexity\Authters\Firewall\Key\FirewallContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\FirewallKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\LocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Value\Credentials;

class SwitchIdentityToken extends Token implements LocalToken
{
    /**
     * @var Credentials
     */
    private $credentials;

    /**
     * @var ContextKey
     */
    private $contextKey;

    /**
     * @var GenericLocalToken
     */
    private $originalToken;

    public function __construct(Identity $identity,
                                Credentials $credentials,
                                ContextKey $contextKey,
                                array $roles,
                                GenericLocalToken $originalToken)
    {
        parent::__construct($roles);

        $this->setIdentity($identity);
        $this->setAuthenticated(true);

        $this->credentials = $credentials;
        $this->contextKey = $contextKey;
        $this->originalToken = $originalToken;
    }

    public function getOriginalToken(): GenericLocalToken
    {
        return $this->originalToken;
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
        return serialize([
            $this->getFirewallKey()->getValue(),
            $this->originalToken,
            parent::serialize()
        ]);
    }

    public function unserialize($serialized)
    {
        [$this->contextKey, $this->originalToken, $parentStr] = unserialize($serialized, [Tokenable::class]);

        parent::unserialize($parentStr);
    }
}