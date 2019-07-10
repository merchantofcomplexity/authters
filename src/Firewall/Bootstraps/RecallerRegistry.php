<?php

namespace MerchantOfComplexity\Authters\Firewall\Bootstraps;

use Closure;
use Illuminate\Contracts\Cookie\QueueingFactory;
use Illuminate\Contracts\Foundation\Application;
use MerchantOfComplexity\Authters\Firewall\Builder;
use MerchantOfComplexity\Authters\Guard\Service\Recaller\SimpleRecallerService;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallRegistry;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Recaller\Recallable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Recaller\RecallerProvider;

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

    public function compose(Builder $auth, Closure $make)
    {
        if ($auth->context()->isStateless()) {
            return $make($auth);
        }

        $contextKey = $auth->context()->contextKey();

        // need options in context
        $this->app->bindIf(Recallable::class, function (Application $app) use ($contextKey): Recallable {
            return new SimpleRecallerService(
                $app->make(QueueingFactory::class),
                $app->make(RecallerProvider::class),
                $contextKey
            );
        });

        return $make($auth);
    }
}