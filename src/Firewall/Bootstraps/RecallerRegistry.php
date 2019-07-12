<?php

namespace MerchantOfComplexity\Authters\Firewall\Bootstraps;

use Closure;
use Illuminate\Contracts\Cookie\QueueingFactory;
use Illuminate\Contracts\Foundation\Application;
use MerchantOfComplexity\Authters\Guard\Service\Recaller\SimpleRecallerService;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallRegistry;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Recaller\Recallable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Recaller\RecallerProvider;
use MerchantOfComplexity\Authters\Support\Firewall\FirewallAware;

final class RecallerRegistry implements FirewallRegistry
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
        if (!$firewall->context()->isStateless()) {
            $contextKey = $firewall->context()->contextKey();

            // todo options in context
            $this->registerRecallerService($contextKey);
        }

        return $make($firewall);
    }

    protected function registerRecallerService(ContextKey $contextKey): void
    {
        $this->app->bindIf(Recallable::class,
            function (Application $app) use ($contextKey): Recallable {
                return new SimpleRecallerService(
                    $app->make(QueueingFactory::class),
                    $app->make(RecallerProvider::class),
                    $contextKey
                );
            });
    }
}