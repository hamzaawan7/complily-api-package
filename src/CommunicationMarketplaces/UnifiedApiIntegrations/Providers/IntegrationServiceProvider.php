<?php

namespace CommunicationMarketplaces\UnifiedApiIntegrations\Providers;

use Illuminate\Support\ServiceProvider;
use CommunicationMarketplaces\UnifiedApiIntegrations\Services\OAuthService;

class IntegrationServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        // Register OAuthService dynamically
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
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../Config/oauth_service.php' => config_path('oauth_service.php')
        ], 'config');
    }
}
