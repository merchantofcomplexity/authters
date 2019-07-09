<?php

namespace MerchantOfComplexity\Authters\Application\Providers;

use Illuminate\Support\AggregateServiceProvider;

class AuthtersServiceProvider extends AggregateServiceProvider
{
    protected $providers = [
        ConfigServiceProvider::class
    ];
}