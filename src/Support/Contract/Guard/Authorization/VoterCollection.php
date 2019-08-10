<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization;

use Generator;

interface VoterCollection
{
    public function add(string $voter): VoterCollection;

    public function make(): Generator;

    /**
     * @return string[]
     */
    public function all(): array;

    public function isEmpty(): bool;

    public function count(): int;
}