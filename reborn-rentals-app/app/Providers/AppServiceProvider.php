<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Test Odoo connection on application boot
        $this->testOdooConnection();
    }

    /**
     * Test Odoo connection when application boots
     * 
     * This verifies that Odoo configuration is correct and the service is reachable.
     * Logs the result for debugging purposes.
     */
    protected function testOdooConnection(): void
    {
        try {
            // Check if Odoo configuration exists
            $url = config('services.odoo.url');
            $database = config('services.odoo.database');
            $username = config('services.odoo.username');
            $apiKey = config('services.odoo.api_key');

            if (empty($url) || empty($database) || empty($username) || empty($apiKey)) {
                Log::warning('⚠️ [ODOO] Connection test skipped: Configuration incomplete', [
                    'url_configured' => !empty($url),
                    'database_configured' => !empty($database),
                    'username_configured' => !empty($username),
                    'api_key_configured' => !empty($apiKey),
                    'note' => 'Please configure Odoo settings in .env file',
                ]);
                return;
            }

            Log::info('🔍 [ODOO] Testing connection on application boot', [
                'url' => $url,
                'database' => $database,
                'username' => $username,
            ]);

            // Create Odoo client and test authentication
            $odoo = new \App\Services\Odoo\OdooClient();
            $authenticated = $odoo->authenticate();

            if ($authenticated) {
                $uid = $odoo->getUid();
                Log::info('✅ [ODOO] Connection test PASSED', [
                    'url' => $url,
                    'database' => $database,
                    'uid' => $uid,
                    'status' => 'connected',
                    'note' => 'Odoo is reachable and authentication successful',
                ]);
            } else {
                Log::warning('⚠️ [ODOO] Connection test: Authentication returned false', [
                    'url' => $url,
                    'database' => $database,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('❌ [ODOO] Connection test FAILED', [
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'url' => config('services.odoo.url'),
                'database' => config('services.odoo.database'),
                'note' => 'Odoo connection failed. Check configuration and network connectivity.',
            ]);
        }
    }
}
