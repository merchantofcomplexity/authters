<?php

namespace MerchantOfComplexity\Authters\Guard\Service\Recaller;

use Illuminate\Support\Str;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Recaller\RecallerIdentifier as BaseRecallerIdentifier;
use MerchantOfComplexity\Authters\Support\Contract\Value\Value;
use MerchantOfComplexity\Authters\Support\Exception\Assert;

final class RecallerIdentifier implements BaseRecallerIdentifier
{
    const LENGTH = 32;

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
        return new self(Str::random(self::LENGTH));
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