<?php

namespace MerchantOfComplexityTest\Authters\Unit\Support\Events;

use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Events\ContextEvent;
use MerchantOfComplexityTest\Authters\TestCase;

class ContextEventTest extends TestCase
{
    /**
     * @test
     */
    public function it_access_context_name(): void
    {
        $key = $this->prophesize(ContextKey::class);
        $key->getValue()->willReturn('foo');

        $event = new ContextEvent($key->reveal());

        $this->assertEquals('foo', $event->getName());
    }

    /**
     * @test
     */
    public function it_access_session_name(): void
    {
        $key = $this->prophesize(ContextKey::class);
        $key->getValue()->willReturn('foo');

        $event = new ContextEvent($key->reveal());

        $this->assertEquals('_firewall_foo', $event->sessionName());
    }

    /**
     * @test
     */
    public function it_can_serialized_session_name(): void
    {
        $key = $this->prophesize(ContextKey::class);
        $key->getValue()->willReturn('foo');

        $event = new ContextEvent($key->reveal());

        $this->assertEquals('_firewall_foo', (string)$event);
    }
}