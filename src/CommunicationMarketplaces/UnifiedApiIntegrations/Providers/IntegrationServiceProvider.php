<?php

namespace CommunicationMarketplaces\UnifiedApiIntegrations\Providers;

use Illuminate\Support\ServiceProvider;
use CommunicationMarketplaces\UnifiedApiIntegrations\Services\OAuthService;

class IntegrationServiceProvider extends ServiceProvider
{
    /**
     * Register services dynamically.
     *
     * @return void
     */
    public function register()
    {
        // Register the OAuthService
        $this->app->singleton(OAuthService::class, function ($app, array $params) {
            return new OAuthService(
                $params['authUrl'],
                $params['tokenUrl'],
                $params['revokeUrl'],
                $params['clientId'],
                $params['clientSecret'],
                $params['redirectUri']
            );
        });
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        // Optionally publish a configuration file
        $this->publishes([
            __DIR__ . '/../Config/oauth_service.php' => config_path('oauth_service.php')
        ], 'config');
    }
}
