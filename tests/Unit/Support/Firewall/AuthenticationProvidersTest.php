<?php

namespace MerchantOfComplexityTest\Authters\Unit\Support\Firewall;

use Illuminate\Container\Container;
use MerchantOfComplexity\Authters\Exception\InvalidArgumentException;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\AuthenticationProvider;
use MerchantOfComplexity\Authters\Support\Firewall\AuthenticationProviders;
use MerchantOfComplexityTest\Authters\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use ReflectionClass;

class AuthenticationProvidersTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed_with_empty_providers()
    {
        $providers = new AuthenticationProviders();

        $callback = function () {
            return $this->prophesize(AuthenticationProvider::class)->reveal();
        };

        $providers->add($callback);

        $resolved = $providers($this->container, $this->context->reveal());

        $provider = array_shift($resolved);

        $this->assertInstanceOf(AuthenticationProvider::class, $provider);
    }

    /**
     * @test
     */
    public function it_can_be_constructed_with_callable_provider(): void
    {
        $callback = function () {
            return $this->prophesize(AuthenticationProvider::class)->reveal();
        };

        $providers = new AuthenticationProviders($callback);

        $resolved = $providers($this->container, $this->context->reveal());

        $provider = array_shift($resolved);

        $this->assertInstanceOf(AuthenticationProvider::class, $provider);
    }

    /**
     * @test
     */
    public function it_can_add_authentication_provider_as_string(): void
    {
        $providers = new AuthenticationProviders();

        $this->container->bind('foo', function () {
            return function () {
                return $this->prophesize(AuthenticationProvider::class)->reveal();
            };
        });

        $providers->add('foo');

        $resolved = $providers($this->container, $this->context->reveal());

        $provider = array_shift($resolved);

        $this->assertInstanceOf(AuthenticationProvider::class, $provider);
    }

    /**
     * @test
     */
    public function it_cache_resolved_service(): void
    {
        $providers = new AuthenticationProviders();

        $ref = new ReflectionClass($providers);
        $resolved = $ref->getProperty('resolved');
        $resolved->setAccessible(true);

        $this->assertNull($resolved->getValue($providers));

        $callback = function () {
            return $this->prophesize(AuthenticationProvider::class)->reveal();
        };

        $providers->add($callback);
        $resolvedProviders = $providers($this->container, $this->context->reveal());

        $provider = array_shift($resolvedProviders);

        $this->assertInstanceOf(AuthenticationProvider::class, $provider);

        $this->assertIsIterable($resolved->getValue($providers));
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage No authentication providers has been registered
     */
    public function it_raise_exception_with_empty_provider(): void
    {
        $providers = new AuthenticationProviders();

        $providers($this->container, $this->context->reveal());
    }

    /**
     * @var Container
     */
    private $container;

    /**
     * @var ObjectProphecy
     */
    private $context;

    protected function setUp()
    {
        parent::setUp();

        $this->container = new Container();
        $this->context = $this->prophesize(FirewallContext::class);
    }
}