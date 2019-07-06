<?php

namespace MerchantOfComplexity\Authters\Domain\User;

use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use MerchantOfComplexity\Authters\Support\Contract\Value\Value;
use MerchantOfComplexity\DevShared\Values\Identity\IdentityFactory;
use Ramsey\Uuid\UuidInterface;

final class IdentityId implements IdentifierValue
{
    /**
     * @var IdentityFactory
     */
    private $factory;

    private function __construct(IdentityFactory $factory)
    {
        $this->factory = $factory;
    }

    public static function nextIdentity(): self
    {
        return new self(IdentityFactory::nextIdentity());
    }

    public static function fromString($uid): self
    {
        return new self(IdentityFactory::fromString($uid));
    }

    public function sameValueAs(Value $aValue): bool
    {
        return $aValue instanceof $this && $this->getUid()->equals($aValue->getUid());
    }

    public function getUid(): UuidInterface
    {
        return $this->factory->getUid();
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