<?php

namespace MerchantOfComplexity\Authters\Application\Providers;

use Illuminate\Support\ServiceProvider;
use MerchantOfComplexity\Authters\Application\Http\Middleware\ContextEventAware;
use MerchantOfComplexity\Authters\Firewall\Manager;
use MerchantOfComplexity\Authters\Guard\Authentication\GenericTrustResolver;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\AnonymousToken;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\DevShared\Support\Auth\TrustResolver;

class AuthenticationServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function register(): void
    {
        $this->app->singleton(TokenStorage::class);
        $this->app->singleton(ContextEventAware::class);

        $this->app->bind(TrustResolver::class, function () {
            return new GenericTrustResolver(
                AnonymousToken::class,
                'recaller_token_todo'
            );
        });

        $this->app->singleton(Manager::class);
    }

    public function provides(): array
    {
        return [TokenStorage::class, ContextEventAware::class, TrustResolver::class, Manager::class];
    }
}