<?php

namespace MerchantOfComplexityTest\Authters\Unit\Firewall\Bootstraps;

use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Application as App;
use MerchantOfComplexity\Authters\Firewall\Bootstraps\AuthenticatableRegistry;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallProvision;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Authenticatable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\AuthenticationProvider;
use MerchantOfComplexity\Authters\Support\Firewall\AuthenticationProviders;
use MerchantOfComplexity\Authters\Support\Firewall\FirewallAware;
use MerchantOfComplexityTest\Authters\TestCase;

class AuthenticatableRegistryTest extends TestCase
{
    /**
     * @test
     */
    public function it_register_authentication_manager(): void
    {
        $app = $this->getApplication();
        $this->assertFalse($app->bound(Authenticatable::class));

        //

        $registry = new AuthenticatableRegistry($app);
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