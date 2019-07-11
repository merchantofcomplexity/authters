<?php

namespace MerchantOfComplexity\Authters\Application\Providers;

use Illuminate\Support\ServiceProvider;
use MerchantOfComplexity\Authters\Application\Http\Middleware\ContextEventAware;
use MerchantOfComplexity\Authters\Firewall\Manager;
use MerchantOfComplexity\Authters\Guard\Authentication\GenericTrustResolver;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\DefaultTokenStorage;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\AnonymousToken;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\RecallerToken;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TrustResolver;

class AuthenticationServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function register(): void
    {
        $this->app->singleton(TokenStorage::class, DefaultTokenStorage::class);

        $this->app->singleton(ContextEventAware::class);

        $this->app->bind(TrustResolver::class, function () {
            return new GenericTrustResolver(
                AnonymousToken::class,
                RecallerToken::class
            );
        });

        $this->app->singleton(Manager::class);
    }

    public function provides(): array
    {
        return [TokenStorage::class, TrustResolver::class, ContextEventAware::class, Manager::class];
    }
}