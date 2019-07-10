<?php

namespace MerchantOfComplexity\Authters\Application\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TrustResolver;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\AuthorizationChecker;

class DirectiveServiceProvider extends ServiceProvider
{
    public function boot(TokenStorage $tokenStorage, TrustResolver $trustResolver)
    {
        // do not resolve Authorization checker as dependency auth manager is not set

        Blade::if('isAnonymous', function () use ($tokenStorage, $trustResolver) {
            return $trustResolver->isAnonymous($tokenStorage->getToken());
        });

        Blade::if('isRemembered', function () use ($tokenStorage, $trustResolver) {
            return $trustResolver->isRemembered($tokenStorage->getToken());
        });

        Blade::if('isFullyAuthenticated', function () use ($tokenStorage, $trustResolver) {
            return $trustResolver->isFullyAuthenticated($tokenStorage->getToken());
        });

        Blade::if('isGranted', function ($expression) {
            return app(AuthorizationChecker::class)
                ->isGranted(app(TokenStorage::class)->getToken(), [$expression], request());
        });

        Blade::if('isNotGranted', function ($expression) {
            return false === app(AuthorizationChecker::class)
                    ->isGranted(app(TokenStorage::class)->getToken(), [$expression], request());
        });
    }
}