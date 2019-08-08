<?php

namespace MerchantOfComplexityTest\Authters\Unit\Support\Firewall;

use Illuminate\Container\Container;
use MerchantOfComplexity\Authters\Exception\InvalidArgumentException;
use MerchantOfComplexity\Authters\Exception\RuntimeException;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallProvision;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\AuthenticationProvider;
use MerchantOfComplexity\Authters\Support\Firewall\FirewallAware;
use MerchantOfComplexity\Authters\Support\Firewall\IdentityProviders;
use MerchantOfComplexityTest\Authters\TestCase;

class FirewallAwareTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed(): void
    {
        $firewall = $this->getFirewallAwareInstance();

        $this->assertEquals([
            'foo' => false,
            'bar' => false
        ], $firewall->getServices());

        $this->assertEquals('baz', $firewall->getName());
    }

    /**
     * @test
     */
    public function it_add_sorted_services(): void
    {
        $firewall = $this->getFirewallAwareInstance();

        $firewall->addPreService('pre', function () {
        });
        $firewall->addPostService('post', function () {
        });
        $firewall->addService('bar', function () {
        });
        $firewall->addService('foo', function () {
        });

        $this->assertEquals(['pre', 'foo', 'bar', 'post'], array_keys($firewall->allServices()));
    }


    /**
     * @test
     * @expectedException InvalidArgumentException
     * @@expectedExceptionMessage service bar_bar does not exists
     */
    public function it_raise_exception_when_resolving_provision_is_not_configured(): void
    {
        $firewall = new FirewallAware('baz', 'foo');

        $provision = $this->prophesize(FirewallProvision::class)->reveal();

        $firewall->resolveProvisionService('bar_bar', $provision);
    }

    /**
     * @test
     */
    public function it_resolved_string_provision_services(): void
    {
        $firewall = new FirewallAware('baz', 'fqcn_provision');

        $this->assertEquals(['fqcn_provision' => false], $firewall->getServices());

        $provision = $this->prophesize(FirewallProvision::class);
        $provision->serviceId()->willReturn('foo');
        $provision = $provision->reveal();

        $firewall->resolveProvisionService('fqcn_provision', $provision);

        $this->assertEquals(['foo' => $provision], $firewall->getServices());

        $this->assertArrayNotHasKey('fqcn_provision', $firewall->getServices());
    }

    /**
     * @test
     */
    public function it_compare_firewall_name(): void
    {
        $firewall = $this->getFirewallAwareInstance();

        $this->assertTrue($firewall->isFirewall('baz'));
        $this->assertFalse($firewall->isFirewall('bar'));
    }

    /**
     * @test
     */
    public function it_add_authentication_provider_to_firewall(): void
    {
        $firewall = $this->getFirewallAwareInstance();

        $provider = function () {
            return $this->prophesize(AuthenticationProvider::class)->reveal();
        };

        $firewall->addProvider($provider);

        $authProviders = $firewall->getProviders();

        $container = new Container();
        $context = $this->prophesize(FirewallContext::class);
        $authProviders = $authProviders($container, $context->reveal());

        $this->assertInstanceOf(AuthenticationProvider::class, array_shift($authProviders));
    }

    /**
     * @test
     */
    public function it_set_firewall_context(): void
    {
        $context = $this->prophesize(FirewallContext::class)->reveal();

        $firewall = $this->getFirewallAwareInstance();

        $firewall->setContext($context);

        $this->assertSame($context, $firewall->context());
    }

    /**
     * @test
     */
    public function it_inject_identity_providers(): void
    {
        $firewall = $this->getFirewallAwareInstance();

        $providers = new IdentityProviders('foo');
        $firewall->setIdentityProviders($providers);

        $this->assertSame($providers, $firewall->identityProviders());
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Service id baz does not exists in config
     */
    public function it_raise_exception_if_it_add_non_configured_service(): void
    {
        $firewall = $this->getFirewallAwareInstance();

        $firewall->addService('baz', function () {
        });
    }

    /**
     * @test
     * @expectedException RuntimeException
     * @expectedExceptionMessage Service id foo has not been resolved
     */
    public function it_raise_exception_when_provision_service_has_not_been_resolved(): void
    {
        $firewall = new FirewallAware('baz', 'foo');

        $firewall->allServices();
    }

    /**
     * @test
     * @expectedException \MerchantOfComplexity\Authters\Exception\InvalidArgumentException
     * @expectedExceptionMessage Authentication services for firewall baz can not be empty
     */
    public function it_raise_exception_when_firewall_services_are_empty(): void
    {
        $firewall = new FirewallAware('baz');

        $firewall->allServices();
    }

    private function getFirewallAwareInstance(): FirewallAware
    {
        $services = ['foo', 'bar'];

        return new FirewallAware('baz', ...$services);
    }
}