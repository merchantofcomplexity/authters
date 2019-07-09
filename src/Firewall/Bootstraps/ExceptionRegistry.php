<?php

namespace MerchantOfComplexity\Authters\Firewall\Bootstraps;

use Closure;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Foundation\Application;
use MerchantOfComplexity\Authters\Exception\FirewallExceptionHandler;
use MerchantOfComplexity\Authters\Exception\RuntimeException;
use MerchantOfComplexity\Authters\Firewall\Builder;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallRegistry;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TrustResolver;

final class ExceptionRegistry implements FirewallRegistry
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
        $exceptionId = $this->registerFirewallExceptionHandler($auth->context());

        $this->app->resolving(ExceptionHandler::class,
            function (ExceptionHandler $handler) use ($exceptionId): void {
                if (!method_exists($handler, 'setFirewallHandler')) {
                    throw new RuntimeException(
                        "Method setFirewallHandler does not exists on " . ExceptionHandler::class
                    );
                }

                $handler->setFirewallHandler($exceptionId);
            });

        return $make($auth);
    }

    private function registerFirewallExceptionHandler(FirewallContext $context): string
    {
        $alias = 'firewall.exception_handler.' . $context->contextKey()->getValue();

        $this->app->bind($alias, function (Application $app) use ($context) {
            return new FirewallExceptionHandler(
                $app->make(TokenStorage::class),
                $app->make(TrustResolver::class),
                $context->contextKey(),
                $context->isStateless(),
                $context->entryPointId() ? $app->make($context->entryPointId()) : null,
                $context->unauthorizedId() ? $app->make($context->unauthorizedId()) : null
            );
        });

        return $alias;
    }
}