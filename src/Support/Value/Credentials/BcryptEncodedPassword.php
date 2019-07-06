<?php

namespace MerchantOfComplexity\Authters\Support\Value\Credentials;

use MerchantOfComplexity\Authters\Support\Contract\Value\EncodedCredentials;
use MerchantOfComplexity\Authters\Support\Contract\Value\Value;
use MerchantOfComplexity\Authters\Support\Exception\Assert;

final class BcryptEncodedPassword implements EncodedCredentials
{
    const ALGORITHM = PASSWORD_BCRYPT;

    /**
     * @var string
     */
    private $encodedPassword;

    protected function __construct(string $encodedPassword)
    {
        $this->encodedPassword = $encodedPassword;
    }

    public static function fromClearConfirmedPassword(ClearConfirmedPassword $password): self
    {
        $encodedPassword = password_hash($password->getValue(), self::ALGORITHM);

        return new self($encodedPassword);
    }

    public function verify(ClearPassword $password): bool
    {
        return password_verify($password->getValue(), $this->encodedPassword);
    }

    public static function fromString(string $encodedPassword): self
    {
        Assert::length($encodedPassword, 60);

        $hashed = password_get_info($encodedPassword);

        Assert::same(self::ALGORITHM, $hashed['algo'], "Password is invalid");

        return new self($encodedPassword);
    }

    public function getValue(): string
    {
        return $this->encodedPassword;
    }

    public function sameValueAs(Value $aValue): bool
    {
        return $aValue instanceof $this && $this->getValue() === $aValue->getValue();
    }
}