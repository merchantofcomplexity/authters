<?php

namespace MerchantOfComplexityTest\Authters\Unit\Firewall\Bootstraps;

use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Application as App;
use MerchantOfComplexity\Authters\Application\Http\Middleware\AnonymousAuthentication;
use MerchantOfComplexity\Authters\Firewall\Bootstraps\AnonymousRegistry;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;
use MerchantOfComplexity\Authters\Support\Firewall\FirewallAware;
use MerchantOfComplexityTest\Authters\TestCase;
use Prophecy\Argument;

class AnonymousRegistryTest extends TestCase
{
    /**
     * @test
     */
    public function it_does_not_register_anonymous_service_if_context_is_not_anonymous(): void
    {
        $this->context->isAnonymous()->willReturn(false);
        $this->firewall->context()->willReturn($this->context->reveal());

        $registry = new AnonymousRegistry($this->getApplication());

        $this->firewall->addPostService()->shouldNotBeCalled();
        $this->firewall->addProvider()->shouldNotBeCalled();

        $registry->compose($this->firewall->reveal(), function () {
            $this->assertTrue(true);
        });
    }

    /**
     * @test
     */
    public function it_register_anonymous_service(): void
    {
        $this->context->isAnonymous()->willReturn(true);
        $this->firewall->context()->willReturn($this->context->reveal());

        $app = $this->getApplication();
        $registry = new AnonymousRegistry($app);

        $this->firewall
            ->addPostService('anonymous', Argument::type('callable'))
            ->willReturn($this->firewall);


        $this->firewall->addProvider(Argument::type('closure'))
            ->willReturn($this->firewall);

        $registry->compose($this->firewall->reveal(), function () {
            $this->assertTrue(true);
        });
    }

    protected function getApplication(): Application
    {
        $app = new App();
        $app::setInstance(new Container());

        return $app;
    }

    private $firewall;
    private $context;

    protected function setUp(): void
    {
        $this->firewall = $this->prophesize(FirewallAware::class);
        $this->context = $this->prophesize(FirewallContext::class);
    }
}