<?php

namespace MerchantOfComplexity\Authters\Firewall;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pipeline\Pipeline;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline as BasePipeline;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallProvision;
use MerchantOfComplexity\Authters\Support\Firewall\FirewallAware;

final class Processor
{
    /**
     * @var Pipeline
     */
    private $pipeline;

    /**
     * @var Application
     */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->pipeline = new BasePipeline($app);
    }

    public function process(FirewallAware $firewallAware, Request $request, array $bootstraps): iterable
    {
        $services = $this->buildFirewall($firewallAware, $bootstraps);

        return $this->generateService($services, $firewallAware->context(), $request);
    }

    protected function generateService(array $services, FirewallContext $context, Request $request): iterable
    {
        return array_filter(
            array_map(function ($service) use ($context, $request) {
                if (is_string($service)) {
                    $service = $this->app->get($service);
                }

                if ($service instanceof FirewallProvision) {
                    $service = $service->match($request) ? $service->callAuthentication() : null;
                }

                if (is_callable($service)) {
                    return $service($this->app, $context, $request);
                }

                return null;
            }, $services)
        );
    }

    protected function buildFirewall(FirewallAware $firewallAware, array $bootstraps): array
    {
        return $this->pipeline
            ->via('compose')
            ->through($bootstraps)
            ->send($firewallAware)
            ->then(function () use ($firewallAware): array {
                return $firewallAware->allServices();
            });
    }
}