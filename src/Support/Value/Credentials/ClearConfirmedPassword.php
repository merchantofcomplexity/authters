<?php

namespace MerchantOfComplexity\Authters\Support\Value\Credentials;

use MerchantOfComplexity\Authters\Support\Contract\Value\Value;
use MerchantOfComplexity\Authters\Support\Exception\Assert;

final class ClearConfirmedPassword extends ClearPassword
{
    public function __construct(string $password, string $passwordConfirmation)
    {
        Assert::same($passwordConfirmation, $password);

        parent::__construct($password);
    }

    public function sameValueAs(Value $aValue): bool
    {
        return $aValue instanceof $this && $this->getValue() === $aValue->getValue();
    }
}