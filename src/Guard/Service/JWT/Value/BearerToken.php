<?php

namespace MerchantOfComplexity\Authters\Guard\Service\JWT\Value;

use MerchantOfComplexity\Authters\Support\Contract\Value\Credentials;
use MerchantOfComplexity\Authters\Support\Contract\Value\Value;
use MerchantOfComplexity\Authters\Support\Exception\Assert;

final class BearerToken implements Credentials
{
    /**
     * @var string
     */
    private $token;

    private function __construct(string $token)
    {
        $this->token = $token;
    }

    public static function fromString($bearer): self
    {
        $message = "Bearer token is invalid";

        Assert::string($bearer, $message);
        Assert::notBlank($bearer, $message);

        return new self($bearer);
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