<?php

namespace MerchantOfComplexity\Authters\Support\Guard\Authorization;

use Generator;
use Illuminate\Contracts\Foundation\Application;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\VoterCollection;

class Voters implements VoterCollection
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var iterable;
     */
    private $voters;

    public function __construct(Application $app, string ...$voters)
    {
        $this->app = $app;
        $this->voters = $voters;
    }

    public function add(string $voter): VoterCollection
    {
        $this->voters[] = $voter;

        return $this;
    }

    public function make(): Generator
    {
        foreach ($this->voters as $voter) {
            yield $this->app->get($voter);
        }
    }

    public function all(): array
    {
        return $this->voters;
    }

    public function isEmpty(): bool
    {
        return empty($this->voters);
    }

    public function count(): int
    {
        return count($this->voters);
    }
}