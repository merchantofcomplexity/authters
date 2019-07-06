<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Token;

use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\LocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Value\Credentials;

final class GenericLocalToken extends Token implements LocalToken
{
    /**
     * @var Credentials
     */
    private $credentials;

    public function __construct($user, Credentials $credentials, array $roles = [])
    {
        parent::__construct($roles);

        $this->setIdentity($user);

        $this->credentials = $credentials;

        $this->hasRoles() and $this->setAuthenticated(true);
    }

    public function getCredentials(): Credentials
    {
       return $this->credentials;
    }

    public function serialize(): string
    {
        return serialize([$this->credentials, parent::serialize()]);
    }

    public function unserialize($serialized)
    {
        [$this->credentials, $parentStr] = unserialize($serialized, [Tokenable::class]);

        parent::unserialize($parentStr);
    }
}