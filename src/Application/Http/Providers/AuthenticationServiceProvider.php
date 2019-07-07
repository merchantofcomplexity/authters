<?php

namespace MerchantOfComplexity\Authters\Application\Http\Providers;

use Illuminate\Support\ServiceProvider;
use MerchantOfComplexity\Authters\Application\Http\Middleware\ContextEventMiddleware;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\DefaultTokenStorage;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Events\ContextEvent;

class AuthenticationServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public $singletons = [
        TokenStorage::class => DefaultTokenStorage::class,
        ContextEvent::class => ContextEvent::class,
        ContextEventMiddleware::class => ContextEventMiddleware::class,
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
            TokenStorage::class, ContextEvent::class, ContextEventMiddleware::class
        ];
    }
}