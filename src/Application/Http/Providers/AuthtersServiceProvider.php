<?php

namespace MerchantOfComplexity\Authters\Application\Http\Providers;

use Illuminate\Support\AggregateServiceProvider;

class AuthtersServiceProvider extends AggregateServiceProvider
{
    protected $providers = [
        ConfigServiceProvider::class
    ];
}