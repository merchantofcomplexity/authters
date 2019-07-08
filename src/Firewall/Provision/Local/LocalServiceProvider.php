<?php

namespace MerchantOfComplexity\Authters\Firewall\Provision\Local;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MerchantOfComplexity\Authters\Application\Http\Middleware\LocalAuthentication;
use MerchantOfComplexity\Authters\Application\Http\Middleware\LogoutAuthentication;
use MerchantOfComplexity\Authters\Firewall\FirewallManager;
use MerchantOfComplexity\Authters\Firewall\Provision\Service\DefaultIdentityChecker;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;
use MerchantOfComplexity\Authters\Support\Validator\BcryptCredentialsValidator;

class LocalServiceProvider extends ServiceProvider
{
    public function register()
    {
        $manager = new FirewallManager($this->app);

        $manager->addMiddleware('front', 'local-login', function (Application $app) {
            return $app->make(LocalAuthentication::class);
        });

        $manager->registerAuthenticationProvider('front', 'local-login', function (Application $app, FirewallContext $context) {
            return new LocalAuthProvider(
                $app->make($context->identityProviderId()),
                new DefaultIdentityChecker(),
                new BcryptCredentialsValidator()
            );
        });
    }
}