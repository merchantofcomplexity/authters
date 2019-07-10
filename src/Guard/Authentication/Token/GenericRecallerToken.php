<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Token;

use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\FirewallKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\RecallerToken;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Value\Credentials;
use MerchantOfComplexity\Authters\Support\Value\Credentials\EmptyCredentials;

final class GenericRecallerToken extends Token implements RecallerToken
{
    /**
     * @var ContextKey
     */
    private $contextKey;

    public function __construct(Identity $identity, ContextKey $contextKey)
    {
        parent::__construct();

        $this->setIdentity($identity);
        $this->contextKey = $contextKey;
    }

    public function getCredentials(): Credentials
    {
        return new EmptyCredentials();
    }

    public function getFirewallKey(): FirewallKey
    {
        return $this->contextKey;
    }

    public function serialize(): string
    {
        return serialize([$this->contextKey, parent::serialize()]);
    }

    public function unserialize($serialized)
    {
        [$this->contextKey, $parentStr] = unserialize($serialized, [Tokenable::class]);

        parent::unserialize($parentStr);
    }
}