<?php

namespace MerchantOfComplexity\Authters\Application\Providers;

use Illuminate\Support\ServiceProvider;
use MerchantOfComplexity\Authters\Application\Http\Middleware\ContextEventAware;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\DefaultTokenStorage;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;

class AuthenticationServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public $singletons = [
        TokenStorage::class => DefaultTokenStorage::class,
        ContextEventAware::class => ContextEventAware::class,
    ];

    public function boot(): void
    {
        //push grant a s last middleware
    }

    public function register(): void
    {
        // register trust resolver

    }

    public function provides(): array
    {
        return [
            TokenStorage::class, ContextEventAware::class
        ];
    }
}