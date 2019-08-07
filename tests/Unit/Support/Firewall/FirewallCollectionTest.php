<?php

namespace MerchantOfComplexityTest\Authters\Unit\Support\Firewall;

use Illuminate\Container\Container;
use Illuminate\Foundation\Application;
use MerchantOfComplexity\Authters\Exception\InvalidArgumentException;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\AuthenticationProvider;
use MerchantOfComplexity\Authters\Support\Firewall\FirewallAware;
use MerchantOfComplexity\Authters\Support\Firewall\FirewallCollection;
use MerchantOfComplexityTest\Authters\TestCase;

class FirewallCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function it_construct_multiple_firewall()
    {
        $firewall = new FirewallCollection($this->app, $this->multipleFirewall());

        $this->assertTrue($firewall->exists('foo'));
        $this->assertTrue($firewall->exists('foo_bar'));
    }

    /**
     * @test
     */
    public function it_assert_firewall_exists(): void
    {
        $firewall = new FirewallCollection($this->app, $this->multipleFirewall());
        $this->assertTrue($firewall->exists('foo'));
    }

    /**
     * @test
     */
    public function it_return_firewall_aware_instance(): void
    {
        $firewalls = new FirewallCollection($this->app, $this->multipleFirewall());

        $firewall = $firewalls->firewallOfName('foo');

        $this->assertInstanceOf(FirewallAware::class, $firewall);
        $this->assertEquals('foo', $firewall->getName());
    }

    /**
     * @test
     */
    public function it_add_known_authentication_service_to_firewall(): void
    {
        $firewalls = new FirewallCollection($this->app, $this->multipleFirewall());

        $firewalls->addService('foo', 'baz', 'bar');

        $services = $firewalls->firewallOfName('foo')->getServices();

        $this->assertEquals(['baz' => 'bar'], $services);
    }

    /**
     * @test
     */
    public function it_add_authentication_provider_to_firewall(): void
    {
        $firewalls = new FirewallCollection($this->app, $this->multipleFirewall());

        $callback = function () {
            return $this->prophesize(AuthenticationProvider::class)->reveal();
        };

        $firewalls->addProvider('foo', $callback);

        $authProviders = $firewalls->firewallOfName('foo')->getProviders();

        $context = $this->prophesize(FirewallContext::class)->reveal();
        $authProviders = $authProviders($this->app, $context);

        $this->assertInstanceOf(AuthenticationProvider::class, array_shift($authProviders));
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage You must provide at least one firewall
     */
    public function it_raise_exception_if_services_are_empty(): void
    {
        new FirewallCollection($this->app, ['authentication' => ['group' => []]]);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unknown firewall name bar_bar
     */
    public function it_raise_exception_of_invalid_firewall(): void
    {
        $firewalls = new FirewallCollection($this->app, $this->multipleFirewall());

        $firewalls->firewallOfName('bar_bar');
    }

    protected function multipleFirewall(): array
    {
        return [
            'authentication' => [
                'group' => [
                    'foo' => [
                        'context' => 'bar',
                        'auth' => [
                            'baz'
                        ]
                    ],

                    'foo_bar' => [
                        'context' => 'bar',
                        'auth' => [
                            'baz_baz'
                        ]
                    ]
                ]
            ]
        ];
    }

    private $app;

    protected function setUp()
    {
        parent::setUp();
        $this->app = new Application();

        $this->app::setInstance(new Container());
    }
}