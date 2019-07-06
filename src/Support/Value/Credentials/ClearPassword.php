<?php

namespace MerchantOfComplexity\Authters\Support\Value\Credentials;

use MerchantOfComplexity\Authters\Support\Contract\Value\ClearCredentials;
use MerchantOfComplexity\Authters\Support\Contract\Value\Value;
use MerchantOfComplexity\Authters\Support\Exception\Assert;

class ClearPassword implements ClearCredentials
{
    const MIN_LENGTH = 8;
    const MAX_LENGTH = 255;

    /**
     * @var string
     */
    private $password;

    public function __construct(string $password)
    {
        Assert::betweenLength($password, self::MIN_LENGTH, self::MAX_LENGTH);

        $this->password = $password;
    }

    public function sameValueAs(Value $aValue): bool
    {
        /** @var ClearPassword $aValue */
        return get_class($this) === get_class($aValue)
            && $this->getValue() === $aValue->getValue();
    }

    public function getValue(): string
    {
        return $this->password;
    }
}