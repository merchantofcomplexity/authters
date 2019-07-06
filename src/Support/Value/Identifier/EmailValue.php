<?php

namespace MerchantOfComplexity\Authters\Support\Value\Identifier;

use MerchantOfComplexity\Authters\Support\Contract\Value\EmailAddress;
use MerchantOfComplexity\Authters\Support\Contract\Value\Value;
use MerchantOfComplexity\Authters\Support\Exception\Assert;

final class EmailValue implements EmailAddress
{
    /**
     * @var string
     */
    private $email;

    protected function __construct(string $email)
    {
        $this->email = $email;
    }

    public static function fromString($email): self
    {
        $message = 'Email address is invalid';

        Assert::string($email, $message);
        Assert::email($email, $message);

        return new self($email);
    }

    public function sameValueAs(Value $aValue): bool
    {
        return $aValue instanceof $this && $this->getValue() === $aValue->getValue();
    }

    public function getValue(): string
    {
        return $this->email;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }
}