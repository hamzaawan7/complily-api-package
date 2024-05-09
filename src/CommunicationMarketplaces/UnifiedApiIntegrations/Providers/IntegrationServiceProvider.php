<?php

namespace CommunicationMarketplaces\UnifiedApiIntegrations\Providers;

use Illuminate\Support\ServiceProvider;
use CommunicationMarketplaces\UnifiedApiIntegrations\Services\UnifiedApiService;

/**
 * Class IntegrationServiceProvider
 * @package CommunicationMarketplaces\UnifiedApiIntegrations\Providers
 */
class IntegrationServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        // Load configuration
        $this->mergeConfigFrom(__DIR__ . '/../Config/integrations.php', 'integrations');

        // Register UnifiedApiService
        $this->app->singleton(UnifiedApiService::class, function ($app, $params) {
            $service = $params['service'];
            return new UnifiedApiService($service);
        });
    }

    /**
     * @return void
     */
    public function boot()
    {
        // Publish the configuration file
        $this->publishes([
            __DIR__ . '/../Config/integrations.php' => config_path('integrations.php')
        ], 'config');
    }
}
