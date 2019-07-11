<?php

namespace MerchantOfComplexity\Authters\Firewall;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use MerchantOfComplexity\Authters\Exception\InvalidArgumentException;
use MerchantOfComplexity\Authters\Firewall\Context\DefaultFirewallContext;
use MerchantOfComplexity\Authters\Firewall\Factory\AuthenticationProviders;
use MerchantOfComplexity\Authters\Firewall\Factory\IdentityProviders;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\MutableFirewallContext;

final class Manager
{
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
    protected $firewall = [];

    /**
     * @var array
     */
    protected $authenticationProviders = [];

    /**
     * @var array
     */
    protected $config;

    public function __construct(Application $app, Processor $processor)
    {
        $this->app = $app;
        $this->config = $app->get('config')->get('authters');
        $this->processor = $processor;
    }

    public function raise(string $name, Request $request): iterable
    {
        $this->assertFirewallExistsInConfig($name);

        $this->assertFirewallIsRegistered($name);

        $this->assertAuthenticationProviderIsRegisteredForFirewall($name);

        return $this->make($name, $request);
    }

    public function addAuthenticationService(string $name, string $serviceId, callable $service): void
    {
        $this->assertServiceIdExists($name, $serviceId);

        $this->firewall[$name][$serviceId] = $service;
    }

    public function addAuthenticationProvider(string $name, callable $service): void
    {
        if (!isset($this->authenticationProviders[$name])) {
            $this->authenticationProviders[$name] = [];
        }

        $this->authenticationProviders[$name] = array_merge($this->authenticationProviders[$name], [$service]);
    }

    public function hasFirewall(string $name): bool
    {
        return array_key_exists($name, $this->fromConfig('authentication.group', []));
    }

    protected function make(string $name, Request $request): iterable
    {
        return $this->processor->process(
            $this->prepareBuilder($name),
            $request,
            $this->determineBootstraps()
        );
    }

    protected function prepareBuilder(string $name): Builder
    {
        return new Builder(
            $this->determineFirewallContext($name),
            new IdentityProviders(...$this->fromConfig('identity_providers', [])),
            new AuthenticationProviders(...$this->authenticationProviders[$name]),
            ...array_values($this->firewall[$name]) // sort services with config
        );
    }

    protected function determineBootstraps(): array
    {
        $registries = $this->fromConfig('authentication.bootstraps', []);

        if (!$registries) {
            throw new InvalidArgumentException("No bootstraps found in configuration");
        }

        return $registries;
    }

    protected function determineFirewallContext(string $name): FirewallContext
    {
        $payload = $this->fromConfig("authentication.group.$name.context", null);

        if (!$payload) {
            throw new InvalidArgumentException(
                "Firewall context is mandatory for firewall name $name"
            );
        }

        if (is_array($payload)) {
            $context = new DefaultFirewallContext();

            $context = $context($payload);
        } else {
            $context = $this->app->get($payload);
        }

        return $context instanceof MutableFirewallContext ? $context->toImmutable() : $context;
    }

    protected function assertFirewallExistsInConfig(string $name): void
    {
        if (!array_key_exists($name, $this->fromConfig("authentication.group", []))) {
            throw new InvalidArgumentException(
                "Firewall name $name not found in configuration"
            );
        }
    }

    protected function assertFirewallIsRegistered(string $name): void
    {
        if (!isset($this->firewall[$name])) {
            throw new InvalidArgumentException(
                "No authentication service has been registered for firewall name $name"
            );
        }
    }

    protected function assertServiceIdExists(string $name, string $serviceId): void
    {
        $this->assertFirewallExistsInConfig($name);

        if (!in_array($serviceId, $this->fromConfig("authentication.group.$name.auth", []))) {
            throw new InvalidArgumentException(
                "Service id $serviceId not found in configuration for firewall name $name"
            );
        }
    }

    protected function assertAuthenticationProviderIsRegisteredForFirewall(string $name): void
    {
        if (!isset($this->authenticationProviders[$name])) {
            throw new InvalidArgumentException(
                "No authentication provider has been registered for firewall name $name"
            );
        }
    }

    protected function fromConfig(string $key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }
}