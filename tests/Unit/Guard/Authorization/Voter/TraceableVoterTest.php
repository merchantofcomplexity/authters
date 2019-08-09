<?php

namespace MerchantOfComplexityTest\Authters\Unit\Guard\Authorization\Voter;

use Illuminate\Contracts\Events\Dispatcher;
use MerchantOfComplexity\Authters\Guard\Authorization\Voter\TraceableVoter;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\Votable;
use MerchantOfComplexity\Authters\Support\Events\VoterEvent;
use MerchantOfComplexityTest\Authters\TestCase;
use Prophecy\Argument;

class TraceableVoterTest extends TestCase
{
    /**
     * @test
     */
    public function it_surrogate_result_decorated_voter(): void
    {
        $voter = $this->prophesize(Votable::class);
        $dispatcher = $this->prophesize(Dispatcher::class);
        $token = $this->prophesize(Tokenable::class);

        $voter->vote($token->reveal(), ['foo'], new \stdClass())->willReturn(1);

        $dispatcher->dispatch(Argument::type(VoterEvent::class))->shouldBeCalled();

        $debugVoter = new TraceableVoter($voter->reveal(), $dispatcher->reveal());
        $result = $debugVoter->vote($token->reveal(), ['foo'], new \stdClass());

        $this->assertEquals(1, $result);
    }

    /**
     * @test
     */
    public function it_access_decorated_voter(): void
    {
        $voter = $this->prophesize(Votable::class);
        $dispatcher = $this->prophesize(Dispatcher::class);

        $debugVoter = new TraceableVoter($voter->reveal(), $dispatcher->reveal());

        $this->assertEquals($voter->reveal(), $debugVoter->getDecoratedVoter());
    }
}