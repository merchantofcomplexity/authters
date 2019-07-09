<?php

namespace MerchantOfComplexity\Authters\Support\Events;

use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;

final class ContextEvent
{
    const PREFIX_SESSION = '_firewall_';

    /**
     * @var ContextKey
     */
    private $contextKey;

    public function __construct(ContextKey $contextKey)
    {
        $this->contextKey = $contextKey;
    }

    public function getName(): string
    {
        return $this->contextKey->getValue();
    }

    public function sessionName(): string
    {
        return self::PREFIX_SESSION . $this->getName();
    }

    public function __toString(): string
    {
        return $this->sessionName();
    }
}