<?php

namespace MerchantOfComplexity\Authters\Firewall;

use Generator;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pipeline\Pipeline;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline as BasePipeline;

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

    public function process(Builder $builder, Request $request, array $bootstraps): Generator
    {
        // checkMe request should/could be used against heavy service
        // or service which could be easily dropped against a matcher

        foreach ($this->makeAuthentication($builder, $bootstraps) as $service) {
            yield $service($this->app, $builder->context());
        }
    }

    private function makeAuthentication(Builder $builder, array $bootstraps): array
    {
        return $this->pipeline
            ->via('compose')
            ->through($bootstraps)
            ->send($builder)
            ->then(function () use ($builder): array {
                return $builder->getRegistries();
            });
    }
}