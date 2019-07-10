<?php

namespace MerchantOfComplexity\Authters\Application\Providers;

use Illuminate\Support\AggregateServiceProvider;

class AuthtersServiceProvider extends AggregateServiceProvider
{
    /**
     * @var array
     */
    protected $providers = [
        ConfigServiceProvider::class,
        AuthenticationServiceProvider::class,
        AuthorizationServiceProvider::class,
        DirectiveServiceProvider::class
    ];
}