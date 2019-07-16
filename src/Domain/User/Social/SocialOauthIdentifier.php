<?php

namespace MerchantOfComplexity\Authters\Domain\User\Social;

use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use MerchantOfComplexity\Authters\Support\Contract\Value\Value;
use MerchantOfComplexity\Authters\Support\Exception\Assert;

final class SocialOauthIdentifier implements IdentifierValue
{
    /**
     * @var string
     */
    private $identityProviderId;

    /**
     * @var SocialProviderName
     */
    private $socialProviderName;

    protected function __construct(string $identityProviderId, SocialProviderName $socialProviderName)
    {
        $this->identityProviderId = $identityProviderId;
        $this->socialProviderName = $socialProviderName;
    }

    public static function fromString(string $socialProviderId, string $socialProviderName): self
    {
        Assert::notEmpty($socialProviderId);

        return new self(
            $socialProviderId,
            SocialProviderName::fromString($socialProviderName)
        );
    }

    public function sameValueAs(Value $aValue): bool
    {
        return $aValue instanceof $this
            && $this->identityProviderId === $aValue->getIdentityProviderId()
            && $this->socialProviderName->sameValueAs($aValue->getSocialProviderName());
    }

    public function getIdentityProviderId(): string
    {
        return $this->identityProviderId;
    }

    public function identify(): array
    {
        return [
            'identity_provider_id' => $this->identityProviderId,
            'social_provider_name' => $this->socialProviderName->getValue()
        ];
    }

    public function getSocialProviderName(): SocialProviderName
    {
        return $this->socialProviderName;
    }

    public function getValue(): array
    {
        return $this->identify();
    }

    public function __toString(): string
    {
        return implode(',', $this->identify());
    }
}