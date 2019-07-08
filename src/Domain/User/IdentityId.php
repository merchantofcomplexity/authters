<?php

namespace MerchantOfComplexity\Authters\Domain\User;

use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use MerchantOfComplexity\Authters\Support\Contract\Value\Value;
use MerchantOfComplexity\Authters\Support\Value\Identifier\IdentifierFactory;
use Ramsey\Uuid\UuidInterface;

final class IdentityId implements IdentifierValue
{
    /**
     * @var IdentifierFactory
     */
    private $factory;

    private function __construct(IdentifierFactory $factory)
    {
        $this->factory = $factory;
    }

    public static function nextIdentity(): self
    {
        return new self(IdentifierFactory::nextIdentity());
    }

    public static function fromString($uid): self
    {
        return new self(IdentifierFactory::fromString($uid));
    }

    public function sameValueAs(Value $aValue): bool
    {
        return $aValue instanceof $this && $this->getUniqueId()->equals($aValue->getUniqueId());
    }

    public function getUniqueId(): UuidInterface
    {
        return $this->factory->getUniqueId();
    }

    public function identify(): string
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        return $this->factory->toString();
    }

    public function __toString(): string
    {
        return $this->getValue();
    }
}