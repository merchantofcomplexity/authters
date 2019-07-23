<?php

namespace MerchantOfComplexity\Authters\Firewall\Bootstraps;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use MerchantOfComplexity\Authters\Application\Http\Middleware\ThrottleLoginAuthentication;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\Authentication;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallRegistry;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Firewall\FirewallAware;

final class ThrottleLoginRegistry implements FirewallRegistry
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
        if ($options = $firewall->context()->throttleLogin()) {
            $serviceId = $this->registerService($firewall->context()->contextKey(), $options);

            $firewall->addPreService('throttle-login', function (Application $app) use ($serviceId) {
                return $app->make($serviceId);
            });
        }

        return $make($firewall);
    }

    protected function registerService(ContextKey $contextKey, array $options): string
    {
        $serviceId = 'firewall.throttle-login.' . $contextKey->getValue();

        $this->app->singleton($serviceId,
            function (Application $app) use ($contextKey, $options): Authentication {
                return new ThrottleLoginAuthentication(
                    $contextKey,
                    $app->make($options['request']),
                    $app->make(TokenStorage::class),
                    $app->make(Dispatcher::class),
                    $app->make(RateLimiter::class),
                    $options['decay'] ?? null,
                    $options['max_attempts'] ?? null
                );
            });

        return $serviceId;
    }
}