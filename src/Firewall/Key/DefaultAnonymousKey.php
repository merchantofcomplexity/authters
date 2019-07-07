<?php

namespace MerchantOfComplexity\Authters\Firewall\Key;

use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\AnonymousKey;
use MerchantOfComplexity\Authters\Support\Contract\Value\Value;
use MerchantOfComplexity\Authters\Support\Exception\Assert;

final class DefaultAnonymousKey implements AnonymousKey
{
    /**
     * @var string
     */
    private $key;

    public function __construct($key)
    {
        $message = 'Firewall anonymous key is invalid';

        Assert::notBlank($key, $message);
        Assert::string($key, $message);

        $this->key = $key;
    }

    public function sameValueAs(Value $aValue): bool
    {
        return get_class($aValue) === get_class($this)
            && $this->getValue() === $aValue->getValue();
    }

    public function getValue(): string
    {
        return $this->key;
    }
}