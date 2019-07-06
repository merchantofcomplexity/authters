<?php

namespace MerchantOfComplexity\Authters\Support\Value\Identifier;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class IdentifierFactory
{
    /**
     * @var UuidInterface
     */
    private $uniqueId;

    public static function nextIdentity(): self
    {
        return new self(Uuid::uuid4());
    }

    public static function fromString(string $uid): self
    {
        return new self(Uuid::fromString($uid));
    }

    private function __construct(UuidInterface $uid)
    {
        $this->uniqueId = $uid;
    }

    public function getUniqueId(): UuidInterface
    {
        return $this->uniqueId;
    }

    public function toString(): string
    {
        return $this->uniqueId->toString();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}