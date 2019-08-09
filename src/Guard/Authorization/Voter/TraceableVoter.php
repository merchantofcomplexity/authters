<?php

namespace MerchantOfComplexity\Authters\Guard\Authorization\Voter;

use Illuminate\Contracts\Events\Dispatcher;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\Votable;
use MerchantOfComplexity\Authters\Support\Events\VoterEvent;

class TraceableVoter implements Votable
{
    /**
     * @var Votable
     */
    private $voter;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    public function __construct(Votable $voter, Dispatcher $dispatcher)
    {
        $this->voter = $voter;
        $this->dispatcher = $dispatcher;
    }

    public function vote(Tokenable $token, array $attributes, object $subject = null): int
    {
        $decision = $this->voter->vote($token, $attributes, $subject);

        $this->dispatcher->dispatch(new VoterEvent(
            $this->voter,
            $attributes,
            $subject,
            $decision
        ));

        return $decision;
    }

    public function getDecoratedVoter(): Votable
    {
        return $this->voter;
    }
}