<?php

namespace MerchantOfComplexity\Authters\Support\Events;

use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\Votable;

final class VoterEvent
{
    /**
     * @var Votable
     */
    private $voter;

    /**
     * @var array
     */
    private $attributes;

    /**
     * @var object
     */
    private $subject;

    /**
     * @var int
     */
    private $vote;

    public function __construct(Votable $voter, array $attributes, object $subject, int $vote)
    {
        $this->voter = $voter;
        $this->attributes = $attributes;
        $this->subject = $subject;
        $this->vote = $vote;
    }

    public function getVoter(): Votable
    {
        return $this->voter;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getSubject(): object
    {
        return $this->subject;
    }

    public function getVote(): int
    {
        return $this->vote;
    }
}