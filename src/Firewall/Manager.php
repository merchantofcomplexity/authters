<?php

namespace MerchantOfComplexity\Authters\Firewall;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use MerchantOfComplexity\Authters\Exception\InvalidArgumentException;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\MutableFirewallContext;
use MerchantOfComplexity\Authters\Support\Firewall\Context\DefaultFirewallContext;
use MerchantOfComplexity\Authters\Support\Firewall\FirewallAware;
use MerchantOfComplexity\Authters\Support\Firewall\FirewallCollection;
use MerchantOfComplexity\Authters\Support\Firewall\IdentityProviders;

final class Manager
{
    /**
     * @var FirewallCollection
     */
    protected $firewall;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Processor
     */
    private $processor;

    /**
     * @var array
     */
    protected $config;

    public function __construct(Application $app, Processor $processor)
    {
        $this->app = $app;
        $this->processor = $processor;
        $this->config = $app->get('config')->get('authters');
        $this->firewall = new FirewallCollection($app, $this->config);
    }

    public function raise(string $name, Request $request): iterable
    {
        return $this->make($name, $request);
    }

    public function addAuthenticationService(string $name, string $serviceId, $service): void
    {
        $this->firewall->addService($name, $serviceId, $service);
    }

    public function addAuthenticationProvider(string $name, $service): void
    {
        $this->firewall->addProvider($name, $service);
    }

    public function hasFirewall(string $name): bool
    {
        return $this->firewall->exists($name);
    }

    protected function make(string $name, Request $request): iterable
    {
        return $this->processor->process(
            $this->prepareFirewall($name),
            $request,
            $this->determineBootstraps()
        );
    }

    protected function prepareFirewall(string $name): FirewallAware
    {
        $firewall = $this->firewall->firewallOfName($name);

        $context = $this->determineFirewallContext($name);
        $firewall->setContext($context);

        $identityProviders = new IdentityProviders(...$this->fromConfig('identity_providers', []));
        $firewall->setIdentityProviders($identityProviders);

        return $firewall;
    }

    protected function determineBootstraps(): array
    {
        if ($registries = $this->fromConfig('authentication.bootstraps', [])) {
            return $registries;
        }

        throw new InvalidArgumentException("No bootstraps found in configuration");
    }

    protected function determineFirewallContext(string $name): FirewallContext
    {
        $payload = $this->fromConfig("authentication.group.$name.context", null);

        switch ($payload) {
            case is_array($payload):
                $context = new DefaultFirewallContext();
                $context = $context($payload);
                break;
            case is_string($payload):
                $context = $this->app->get($payload);
                break;
            default:
                throw new InvalidArgumentException("Firewall context must be an array or a string service");
        }

        return $context instanceof MutableFirewallContext ? $context->toImmutable() : $context;
    }

    protected function fromConfig(string $key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }
}