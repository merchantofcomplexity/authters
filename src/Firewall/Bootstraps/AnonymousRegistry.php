<?php

namespace MerchantOfComplexity\Authters\Firewall\Bootstraps;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use MerchantOfComplexity\Authters\Application\Http\Middleware\AnonymousAuthentication;
use MerchantOfComplexity\Authters\Guard\Authentication\Providers\ProvideAnonymousAuthentication;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\Authentication;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallRegistry;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\AuthenticationProvider;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Firewall\FirewallAware;

final class AnonymousRegistry implements FirewallRegistry
{
    /**
     * @var Application
     */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function compose(FirewallAware $firewall, Closure $make)
    {
        if ($firewall->context()->isAnonymous()) {
            $firewall->addPostService('anonymous',
                function (Application $app, FirewallContext $context): ?Authentication {
                    if (!$app->get(TokenStorage::class)->hasToken()) {
                        return new AnonymousAuthentication($context->anonymousKey());
                    }

                    return null;
                });

            $firewall->addProvider(
                function (Application $app, FirewallContext $context): AuthenticationProvider {
                    return new ProvideAnonymousAuthentication($context->anonymousKey());
                });
        }

        return $make($firewall);
    }
}