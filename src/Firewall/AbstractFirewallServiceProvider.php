<?php

namespace MerchantOfComplexity\Authters\Firewall;

use Illuminate\Support\ServiceProvider;

abstract class AbstractFirewallServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register(): void
    {

    }

    abstract protected function registerAuthenticationService(): callable;
}