<?php

namespace MerchantOfComplexity\Authters\Guard\Service\JWT\Value;

use MerchantOfComplexity\Authters\Support\Contract\Value\Credentials;
use MerchantOfComplexity\Authters\Support\Contract\Value\Value;
use MerchantOfComplexity\Authters\Support\Exception\Assert;

final class JWTTokenCredential implements Credentials
{
    /**
     * @var string
     */
    private $token;

    private function __construct(string $token)
    {
        $this->token = $token;
    }

    public static function fromString(string $token): self
    {
        Assert::notBlank($token);

        return new self($token);
    }

    public function getValue(): string
    {
        return $this->token;
    }

    public function sameValueAs(Value $aValue): bool
    {
        return $aValue instanceof $this && $this->getValue() === $aValue->getValue();
    }
}