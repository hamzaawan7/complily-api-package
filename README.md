# complily-api-package

1. Install dependencies:
   ```bash
   composer require communication-marketplaces/multi-api-integrations

2. Publish Configuration

 ```bash
   php artisan vendor:publish --provider="CommunicationMarketplaces\\UnifiedApiIntegrations\\Providers\\IntegrationServiceProvider" --tag="config"