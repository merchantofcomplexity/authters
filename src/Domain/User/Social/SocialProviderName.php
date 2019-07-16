<?php

namespace MerchantOfComplexity\Authters\Domain\User\Social;

use MerchantOfComplexity\Authters\Support\Contract\Value\Value;
use MerchantOfComplexity\Authters\Support\Exception\Assert;

final class SocialProviderName implements Value
{
    /**
     * @var string
     */
    private $name;

    protected function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function fromString($socialProviderName): self
    {
        Assert::notBlank($socialProviderName);
        Assert::minLength($socialProviderName, 3);

        return new self($socialProviderName);
    }

    public function sameValueAs(Value $aValue): bool
    {
        return $aValue instanceof $this && $this->name === $aValue->getValue();
    }

    public function getValue(): string
    {
        return $this->name;
    }
}