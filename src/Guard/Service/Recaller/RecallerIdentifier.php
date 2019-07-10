<?php

namespace MerchantOfComplexity\Authters\Guard\Service\Recaller;

use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Recaller\RecallerIdentifier as BaseRecallerIdentifier;
use MerchantOfComplexity\Authters\Support\Contract\Value\Value;
use MerchantOfComplexity\Authters\Support\Exception\Assert;

final class RecallerIdentifier implements BaseRecallerIdentifier
{
    const LENGTH = 88;

    /**
     * @var string
     */
    private $identifier;

    protected function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    public static function fromString(string $identifier): BaseRecallerIdentifier
    {
        Assert::length($identifier, self::LENGTH);

        return new self($identifier);
    }

    public static function nextIdentity(): BaseRecallerIdentifier
    {
        return new self(
            base64_encode(random_bytes(64))
        );
    }

    public function sameValueAs(Value $aValue): bool
    {
        return $aValue instanceof $this && $this->identify() === $aValue->identify();
    }

    public function getValue(): string
    {
        return $this->identifier;
    }

    public function identify()
    {
        return $this->getValue();
    }
}