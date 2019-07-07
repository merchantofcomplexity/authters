<?php

namespace MerchantOfComplexity\Authters\Firewall\Key;

use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Value\Value;
use MerchantOfComplexity\Authters\Support\Exception\Assert;

final class FirewallContextKey implements ContextKey
{
    /**
     * @var string
     */
    private $key;

    public function __construct($key)
    {
        $message = 'Firewall context key is invalid';

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