<?php

namespace MerchantOfComplexity\Authters\Support\Events;

use MerchantOfComplexity\Authters\Support\Exception\Assert;

final class ContextEvent
{
    /**
     * @var string
     */
    private $name;

    public function __construct(string $name)
    {
        Assert::notBlank($name);
        Assert::notContains($name, '.', 'Invalid char for context');

        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function sessionName(): string
    {
        return '_firewall_' . $this->name;
    }
}