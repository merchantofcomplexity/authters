<?php

namespace MerchantOfComplexity\Authters\Domain\User\Social;

use MerchantOfComplexity\Authters\Support\Contract\Domain\SocialIdentity;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use MerchantOfComplexity\Authters\Support\Contract\Value\Value;

final class GenericSocialIdentity implements SocialIdentity, IdentifierValue
{
    /**
     * @var array
     */
    private $identity;

    public function __construct(array $identity)
    {
        $this->identity = $identity;
    }

    public function getOauthIdentifier(): SocialOauthIdentifier
    {
        return SocialOauthIdentifier::fromString(
            $this->identity['identity_provider_id'],
            $this->identity['social_provider_name']
        );
    }

    public function getSocialCredentials(): SocialOauthCredentials
    {
        return SocialOauthCredentials::fromString(
            $this->identity['access_token'],
            $this->identity['secret_token'],
            $this->identity['refresh_token']
        );
    }

    public function getIdentifier(): IdentifierValue
    {
        return $this->getOauthIdentifier();
    }

    public function getSocialInfo(): array
    {
        return $this->identity['info'];
    }

    public function getRoles(): array
    {
        return ['ROLE_SOCIAL_USER']; // checkMe
    }

    public function identify(): array
    {
        return $this->getOauthIdentifier()->identify();
    }

    public function sameValueAs(Value $aValue): bool
    {
        return $aValue instanceof $this &&
            $this->getOauthIdentifier()->sameValueAs($aValue->getOauthIdentifier());
    }

    public function getValue(): array
    {
        return $this->identify();
    }
}