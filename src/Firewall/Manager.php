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

        $this->prepareFirewall();
    }

    public function raise(string $name, Request $request): iterable
    {
        $this->assertFirewallIsRegistered($name);

        $this->assertAuthenticationProviderIsRegisteredForFirewall($name);

        return $this->make($name, $request);
    }

    public function addAuthenticationService(string $name, string $serviceId, callable $service): void
    {
        $this->assertFirewallIsRegistered($name);

        $this->assertServiceIdExistsInConfig($name, $serviceId);

        $this->firewall[$name][$serviceId] = $service;
    }

    public function addAuthenticationProvider(string $name, callable $service): void
    {
        $this->assertFirewallExistsInConfig($name);

        $this->authenticationProviders[$name] = array_merge($this->authenticationProviders[$name], [$service]);
    }

    public function hasFirewall(string $name): bool
    {
        return isset($this->firewall[$name]);
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
            ...$this->gatherAuthenticationServices($name)
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

    protected function gatherAuthenticationServices(string $name): array
    {
        $services = $this->fromConfig("authentication.group.$name.auth");

        $sorted = array_fill_keys(array_values($services), false);

        foreach ($this->firewall[$name] as $serviceId => $callable) {
            if (isset($sorted[$serviceId])) {
                $sorted[$serviceId] = $callable;
            }
        }

        foreach ($sorted as $key => $value) {
            if (!$value) {
                throw new InvalidArgumentException("Service name $key registered in config is missing");
            }
        }

        return array_values($sorted);
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

    protected function assertServiceIdExistsInConfig(string $name, string $serviceId): void
    {
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

    protected function prepareFirewall(): void
    {
        $firewall = array_keys($this->fromConfig("authentication.group", []));

        if (!$firewall) {
            throw new InvalidArgumentException("No firewall has been configured");
        }

        foreach ($firewall as $firewallName) {
            $this->firewall[$firewallName] = [];
            $this->authenticationProviders[$firewallName] = [];
        }
    }

    protected function fromConfig(string $key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }
}