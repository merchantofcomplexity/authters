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
        Blade::if('isAnonymous', function () use ($tokenStorage, $trustResolver) {
            return $trustResolver->isAnonymous($tokenStorage->getToken());
        });

        Blade::if('isRemembered', function () use ($tokenStorage, $trustResolver) {
            return $trustResolver->isRemembered($tokenStorage->getToken());
        });

        Blade::if('isFullyAuthenticated', function () use ($tokenStorage, $trustResolver) {
            return $trustResolver->isFullyAuthenticated($tokenStorage->getToken());
        });

        Blade::if('isAuthenticated', function () use ($tokenStorage, $trustResolver) {
            return $trustResolver->isFullyAuthenticated($tokenStorage->getToken())
                || $trustResolver->isRemembered($tokenStorage->getToken());
        });

        Blade::if('isGranted', function ($attributes, object $subject = null) {
            if (!is_array($attributes)) {
                $attributes = [$attributes];
            }

            return app(AuthorizationChecker::class)
                ->isGranted(app(TokenStorage::class)->getToken(), $attributes, $subject ?? request());
        });

        Blade::if('isNotGranted', function ($attributes, object $subject = null) {
            if (!is_array($attributes)) {
                $attributes = [$attributes];
            }

            return app(AuthorizationChecker::class)
                ->isGranted(app(TokenStorage::class)->getToken(), $attributes, $subject ?? request());
        });
    }
}