<?php

namespace MerchantOfComplexity\Authters\Support\Firewall;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use MerchantOfComplexity\Authters\Exception\InvalidArgumentException;

final class FirewallCollection
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var Application
     */
    private $app;

    /**
     * @var array
     */
    private $config;

    public function __construct(Application $app, array $config)
    {
        $this->app = $app;
        $this->config = $config;
        $this->collection = new Collection();

        $this->setupFirewall();
    }

    public function addService(string $name, string $serviceId, $service): void
    {
        $this->assertValidServiceType($service);

        $this->requireFirewall($name)->addService($serviceId, $service);
    }

    public function addProvider(string $name, $provider): void
    {
        $this->assertValidServiceType($provider);

        $this->requireFirewall($name)->addProvider($provider);
    }

    public function hasFirewall(string $name): bool
    {
        return $this->collection->filter(function (FirewallAware $firewall) use ($name) {
            return $firewall->isFirewall($name);
        })->isNotEmpty();
    }

    public function firewallOfName(string $name): FirewallAware
    {
        return $this->requireFirewall($name);
    }

    protected function requireFirewall(string $name): FirewallAware
    {
        if (!$this->hasFirewall($name)) {
            throw new InvalidArgumentException("Unknown firewall name $name");
        }

        return $this->collection->first(function (FirewallAware $firewall) use ($name) {
            return $firewall->isFirewall($name);
        });
    }

    protected function setupFirewall(): void
    {
        $firewall = $this->fromConfig("authentication.group", []);

        if (!$firewall) {
            throw new InvalidArgumentException("You must provide at least one firewall and one authentication service");
        }

        foreach ($firewall as $firewallName => $key) {
            if (!isset($key['auth']) || empty($key['auth'])) {
                $message = "Configuration of each firewall must have an auth key ";
                $message .= "with at least one authentication service";

                throw new InvalidArgumentException($message);
            }

            $auth = array_fill_keys(array_values($key['auth']), false);

            $this->collection->push(new FirewallAware($firewallName, $auth));
        }
    }

    protected function fromConfig(string $key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }

    protected function assertValidServiceType($service): void
    {
        if (is_callable($service)) {
            return;
        }

        if (is_string($service) && (class_exists($service) || $this->app->bound($service))) {
            return;
        }

        throw new InvalidArgumentException("Authentication Service must be a callable, a fqcn class or bound in ioc");
    }
}