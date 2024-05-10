<?php

namespace CommunicationMarketplaces\OAuthService\Providers;

use CommunicationMarketplaces\UnifiedApiIntegrations\Services\OAuthService;
use Illuminate\Support\ServiceProvider;

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
