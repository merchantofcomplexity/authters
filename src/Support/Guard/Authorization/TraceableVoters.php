<?php

namespace MerchantOfComplexity\Authters\Support\Guard\Authorization;

use Generator;
use Illuminate\Contracts\Events\Dispatcher;
use MerchantOfComplexity\Authters\Guard\Authorization\Voter\TraceableVoter;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\VoterCollection;

class TraceableVoters implements VoterCollection
{
    /**
     * @var iterable;
     */
    private $voters;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    public function __construct(Voters $voters, Dispatcher $dispatcher)
    {
        $this->voters = $voters;
        $this->dispatcher = $dispatcher;
    }

    public function add(string $voter): VoterCollection
    {
        $this->voters->add($voter);

        return $this;
    }

    public function make(): Generator
    {
        foreach ($this->voters->make() as $voter) {
            yield new TraceableVoter(
                $voter,
                $this->dispatcher
            );
        }
    }

    public function all(): array
    {
        return $this->voters->all();
    }

    public function isEmpty(): bool
    {
        return $this->voters->isEmpty();
    }

    public function count(): int
    {
        return count($this->voters->all());
    }
}