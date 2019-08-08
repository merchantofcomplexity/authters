<?php

namespace MerchantOfComplexity\Authters\Support\Firewall;

use Illuminate\Contracts\Foundation\Application;
use MerchantOfComplexity\Authters\Exception\InvalidArgumentException;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\MutableFirewallContext;
use MerchantOfComplexity\Authters\Support\Firewall\Context\DefaultFirewallContext;

class FirewallContextResolver
{
    /**
     * @var Application
     */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param string|array $context
     * @return FirewallContext
     */
    public function __invoke($context): FirewallContext
    {
        if (!$context = $this->resolveContext($context)) {
            throw new InvalidArgumentException(
                "Firewall context must be an array, a fqcn or a service bound in ioc"
            );
        }

        return $context instanceof MutableFirewallContext ? $context->toImmutable() : $context;
    }

    protected function resolveContext($context): ?FirewallContext
    {
        if (is_array($context)) {
            return ($this->newDefaultFirewallContext())($context);
        }

        if (is_string($context) && (class_exists($context) || $this->app->bound($context))) {
            return $this->app->get($context);
        }

        return null;
    }

    protected function newDefaultFirewallContext(): FirewallContext
    {
        return new DefaultFirewallContext();
    }
}