<?php

namespace MerchantOfComplexity\Authters\Support\Events;

use Assert\Assertion;

final class ContextEvent
{
    /**
     * @var string
     */
    private $name;

    public function __construct(string $name)
    {
        Assertion::notBlank($name);

        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function sessionName(): string
    {
        return '_authters.' . $this->name;
    }
}