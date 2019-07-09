<?php

namespace MerchantOfComplexity\Authters\Application\Providers;

use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes(
                [$this->getConfigPath() => config_path('authters.php')],
                'config'
            );
        }
    }

    protected function mergeConfig(): void
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'authters');
    }

    protected function getConfigPath(): string
    {
        return __DIR__ . '/../../../../config/authters.php';
    }
}