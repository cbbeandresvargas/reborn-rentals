<?php

namespace App\Services\Odoo;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Odoo JSON-RPC Client - Optimizado para Rental Module
 * 
 * Cliente reutilizable para interactuar con Odoo ERP vía JSON-RPC 2.0.
 * Maneja autenticación, gestión de errores, y provee interfaz genérica
 * para llamar cualquier modelo y método de Odoo.
 * 
 * ⚠️ ESPECIALIZADO PARA MÓDULO DE RENTALS/ALQUILERES
 * 
 * Características principales:
 * - Detección automática de versión Odoo (16+ vs 15-)
 * - Validación de productos alquilables (rent_ok = true)
 * - Cálculo automático de precios por Odoo
 * - Soporte para fechas de alquiler
 * - No requiere creación automática de productos
 * 
 * @version 2.0.0
 * @author Tu Nombre
 * @link https://docs.odoo.com/
 */
class OdooClient
{
    /**
     * Odoo base URL
     */
    protected ?string $url;

    /**
     * Odoo database name
     */
    protected ?string $database;

    /**
     * Odoo username
     */
    protected ?string $username;

    /**
     * Odoo API key (password)
     */
    protected ?string $apiKey;

    /**
     * Authenticated user ID (uid)
     */
    protected ?int $uid = null;

    /**
     * Session ID for authenticated requests
     */
    protected ?string $sessionId = null;

    /**
     * Cache key for storing authentication data
     */
    protected string $cacheKey = 'odoo_auth';

    /**
     * Cache TTL for authentication (in seconds, default 1 hour)
     */
    protected int $authCacheTtl = 3600;

    /**
     * Odoo version (detected automatically)
     */
    protected ?string $odooVersion = null;

    /**
     * Get HTTP client with proper SSL configuration
     * 
     * @param int $timeout Request timeout in seconds
     * @return \Illuminate\Http\Client\PendingRequest
     */
    protected function getHttpClient(int $timeout = 30)
    {
        $client = Http::timeout($timeout)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);
        
        // Disable SSL verification in development/local environments
        // WARNING: Only use this in development, never in production
        if (config('app.env') === 'local' || config('app.debug')) {
            $client = $client->withOptions([
                'verify' => false,
            ]);
        }
        
        return $client;
    }

    /**
     * Create a new Odoo client instance
     */
    public function __construct()
    {
        $this->url = config('services.odoo.url') ? rtrim(config('services.odoo.url'), '/') : null;
        $this->database = config('services.odoo.database');
        $this->username = config('services.odoo.username');
        $this->apiKey = config('services.odoo.api_key');

        // Validate configuration
        if (empty($this->url) || empty($this->database) || empty($this->username) || empty($this->apiKey)) {
            Log::error('OdooClient: Missing required configuration', [
                'url' => !empty($this->url),
                'database' => !empty($this->database),
                'username' => !empty($this->username),
                'api_key' => !empty($this->apiKey),
            ]);
            throw new \RuntimeException('Odoo configuration is incomplete. Please check your .env file.');
        }
    }

    /**
     * Authenticate with Odoo and store session
     * 
     * @return bool True if authentication successful
     * @throws \Exception If authentication fails
     */
    public function authenticate(): bool
    {
        // Check cache first
        $cachedAuth = Cache::get($this->cacheKey);
        if ($cachedAuth && isset($cachedAuth['uid']) && isset($cachedAuth['session_id'])) {
            $this->uid = $cachedAuth['uid'];
            $this->sessionId = $cachedAuth['session_id'];
            $this->odooVersion = $cachedAuth['version'] ?? null;
            Log::info('🔐 [ODOO] Using cached authentication', [
                'uid' => $this->uid,
                'database' => $this->database,
                'version' => $this->odooVersion,
            ]);
            return true;
        }

        try {
            Log::info('🔐 [ODOO] Starting authentication', [
                'url' => $this->url,
                'database' => $this->database,
                'username' => $this->username,
            ]);

            $requestId = rand(1000, 9999);
            $payload = [
                'jsonrpc' => '2.0',
                'method' => 'call',
                'params' => [
                    'service' => 'common',
                    'method' => 'authenticate',
                    'args' => [
                        $this->database,
                        $this->username,
                        $this->apiKey,
                        [],
                    ],
                ],
                'id' => $requestId,
            ];

            $response = $this->getHttpClient(30)->post($this->url . '/jsonrpc', $payload);

            if (!$response->successful()) {
                throw new \Exception('Odoo authentication failed: HTTP ' . $response->status());
            }

            $data = $response->json();

            if (isset($data['error'])) {
                Log::error('OdooClient: Authentication error', ['error' => $data['error']]);
                throw new \Exception('Odoo authentication failed: ' . ($data['error']['message'] ?? 'Unknown error'));
            }

            if (isset($data['result']) && $data['result'] !== false) {
                $this->uid = (int) $data['result'];

                // Detect Odoo version
                $this->detectOdooVersion();

                // Cache authentication data
                Cache::put($this->cacheKey, [
                    'uid' => $this->uid,
                    'session_id' => null,
                    'version' => $this->odooVersion,
                ], $this->authCacheTtl);

                Log::info('✅ [ODOO] Authentication successful', [
                    'uid' => $this->uid,
                    'database' => $this->database,
                    'username' => $this->username,
                    'version' => $this->odooVersion,
                    'cache_ttl' => $this->authCacheTtl . ' seconds',
                ]);

                return true;
            }

            throw new \Exception('Odoo authentication failed: Invalid credentials or response format');

        } catch (\Exception $e) {
            Log::error('OdooClient: Authentication exception', [
                'message' => $e->getMessage(),
                'url' => $this->url,
                'database' => $this->database,
                'username' => $this->username,
            ]);
            throw $e;
        }
    }

    /**
     * Detect Odoo version for compatibility
     * 
     * @return void
     */
    protected function detectOdooVersion(): void
    {
        try {
            $version = $this->call('ir.config_parameter', 'get_param', ['base.version_info']);
            if ($version) {
                $this->odooVersion = is_array($version) ? implode('.', array_slice($version, 0, 2)) : $version;
            }
        } catch (\Exception $e) {
            Log::warning('OdooClient: Could not detect Odoo version', ['error' => $e->getMessage()]);
            $this->odooVersion = 'unknown';
        }
    }

    /**
     * Call an Odoo model method
     * 
     * @param string $model Odoo model name
     * @param string $method Method to call
     * @param array $args Arguments for the method
     * @param array $kwargs Optional keyword arguments
     * @return mixed Response from Odoo
     * @throws \Exception If the call fails
     */
    public function call(string $model, string $method, array $args = [], array $kwargs = [])
    {
        if ($this->uid === null) {
            $this->authenticate();
        }

        try {
            $requestId = rand(1000, 9999);
            $params = [
                $this->database,
                $this->uid,
                $this->apiKey,
                $model,
                $method,
                $args,
                $kwargs,
            ];

            $payload = [
                'jsonrpc' => '2.0',
                'method' => 'call',
                'params' => [
                    'service' => 'object',
                    'method' => 'execute_kw',
                    'args' => $params,
                ],
                'id' => $requestId,
            ];

            Log::debug('📞 [ODOO] Making RPC call', [
                'model' => $model,
                'method' => $method,
                'request_id' => $requestId,
            ]);

            $response = $this->getHttpClient(60)->post($this->url . '/jsonrpc', $payload);

            if (!$response->successful()) {
                throw new \Exception('Odoo RPC call failed: HTTP ' . $response->status());
            }

            $data = $response->json();

            if (isset($data['error'])) {
                $errorMessage = $data['error']['message'] ?? 'Unknown error';
                $errorCode = $data['error']['code'] ?? null;

                Log::error('OdooClient: RPC call error', [
                    'model' => $model,
                    'method' => $method,
                    'error_code' => $errorCode,
                    'error_message' => $errorMessage,
                ]);

                throw new \Exception("Odoo error ({$errorCode}): {$errorMessage}", $errorCode ?? 0);
            }

            if (isset($data['result'])) {
                Log::debug('✅ [ODOO] RPC call successful', [
                    'model' => $model,
                    'method' => $method,
                ]);
                return $data['result'];
            }

            throw new \Exception('Odoo RPC call failed: Invalid response format');

        } catch (\Exception $e) {
            Log::error('OdooClient: RPC call exception', [
                'model' => $model,
                'method' => $method,
                'message' => $e->getMessage(),
            ]);

            if (str_contains($e->getMessage(), 'authentication') || str_contains($e->getMessage(), 'session')) {
                Cache::forget($this->cacheKey);
                $this->uid = null;
                $this->sessionId = null;
                
                $this->authenticate();
                return $this->call($model, $method, $args, $kwargs);
            }

            throw $e;
        }
    }

    /**
     * Clear authentication cache
     * 
     * @return void
     */
    public function clearAuth(): void
    {
        Cache::forget($this->cacheKey);
        $this->uid = null;
        $this->sessionId = null;
        $this->odooVersion = null;
        Log::info('OdooClient: Authentication cache cleared');
    }

    /**
     * Get the authenticated user ID
     * 
     * @return int|null
     */
    public function getUid(): ?int
    {
        return $this->uid;
    }

    /**
     * Check if client is authenticated
     * 
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return $this->uid !== null;
    }

    /**
     * Get Odoo version
     * 
     * @return string|null
     */
    public function getOdooVersion(): ?string
    {
        return $this->odooVersion;
    }

    /**
     * Set authentication cache TTL
     * 
     * @param int $seconds
     * @return self
     */
    public function setAuthCacheTtl(int $seconds): self
    {
        $this->authCacheTtl = $seconds;
        return $this;
    }

    /**
     * Find or create a customer (res.partner) in Odoo
     * 
     * @param array $billingDetails Billing details from Laravel checkout form
     * @return int Partner ID in Odoo
     * @throws \Exception If email is missing or creation fails
     */
    public function findOrCreatePartner(array $billingDetails): int
    {
        if (empty($billingDetails['email'])) {
            throw new \InvalidArgumentException('Email is required to find or create a partner');
        }

        $email = trim($billingDetails['email']);

        try {
            Log::info('👤 [ODOO] Searching for partner by email', ['email' => $email]);
            
            $existingPartners = $this->call('res.partner', 'search_read', [
                [['email', '=', $email]],
            ], [
                'fields' => ['id', 'name', 'email', 'is_company'],
                'limit' => 1,
            ]);

            if (!empty($existingPartners) && isset($existingPartners[0]['id'])) {
                $partnerId = (int) $existingPartners[0]['id'];
                Log::info('✅ [ODOO] Found existing partner', [
                    'partner_id' => $partnerId,
                    'email' => $email,
                ]);
                return $partnerId;
            }

            Log::info('➕ [ODOO] Partner not found, creating new partner', ['email' => $email]);

            $partnerData = $this->mapBillingToPartner($billingDetails);
            $partnerId = $this->call('res.partner', 'create', [$partnerData]);

            if (empty($partnerId)) {
                throw new \Exception('Failed to create partner in Odoo: No ID returned');
            }

            Log::info('✅ [ODOO] Partner created successfully', [
                'partner_id' => $partnerId,
                'email' => $email,
            ]);

            return (int) $partnerId;

        } catch (\Exception $e) {
            Log::error('OdooClient: Error finding or creating partner', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Map Laravel billing details to Odoo partner fields
     * 
     * @param array $billingDetails
     * @return array Partner data formatted for Odoo
     */
    protected function mapBillingToPartner(array $billingDetails): array
    {
        $partnerData = [];

        if (!empty($billingDetails['isCompany']) && !empty($billingDetails['companyName'])) {
            $partnerData['name'] = trim($billingDetails['companyName']);
            $partnerData['is_company'] = true;
        } else {
            $firstName = trim($billingDetails['firstName'] ?? '');
            $lastName = trim($billingDetails['lastName'] ?? '');
            $partnerData['name'] = trim($firstName . ' ' . $lastName);
            $partnerData['is_company'] = false;
        }

        if (!empty($billingDetails['email'])) {
            $partnerData['email'] = trim($billingDetails['email']);
        }

        if (!empty($billingDetails['phone'])) {
            $partnerData['phone'] = trim($billingDetails['phone']);
        }

        if (!empty($billingDetails['addressLine1'])) {
            $partnerData['street'] = trim($billingDetails['addressLine1']);
        }

        if (!empty($billingDetails['addressLine2'])) {
            $partnerData['street2'] = trim($billingDetails['addressLine2']);
        }

        if (!empty($billingDetails['city'])) {
            $partnerData['city'] = trim($billingDetails['city']);
        }

        if (!empty($billingDetails['zip'])) {
            $partnerData['zip'] = trim($billingDetails['zip']);
        }

        if (!empty($billingDetails['state'])) {
            $stateId = $this->findStateId($billingDetails['state']);
            if ($stateId) {
                $partnerData['state_id'] = $stateId;
            }
        }

        if (!empty($billingDetails['country'])) {
            $countryId = $this->findCountryId($billingDetails['country']);
            if ($countryId) {
                $partnerData['country_id'] = $countryId;
            }
        }

        if (!empty($billingDetails['jobTitle'])) {
            $partnerData['function'] = trim($billingDetails['jobTitle']);
        }

        $partnerData['customer_rank'] = 1;

        return $partnerData;
    }

    /**
     * Find Odoo state ID by name
     * 
     * @param string $stateName
     * @return int|null
     */
    protected function findStateId(string $stateName): ?int
    {
        try {
            $states = $this->call('res.country.state', 'search_read', [
                [['name', 'ilike', trim($stateName)]],
            ], [
                'fields' => ['id', 'name'],
                'limit' => 1,
            ]);

            return !empty($states) && isset($states[0]['id']) ? (int) $states[0]['id'] : null;
        } catch (\Exception $e) {
            Log::warning('OdooClient: Error finding state', [
                'state' => $stateName,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Find Odoo country ID by name
     * 
     * @param string $countryName
     * @return int|null
     */
    protected function findCountryId(string $countryName): ?int
    {
        try {
            $countries = $this->call('res.country', 'search_read', [
                [['name', '=', trim($countryName)]],
            ], [
                'fields' => ['id', 'name'],
                'limit' => 1,
            ]);

            if (!empty($countries) && isset($countries[0]['id'])) {
                return (int) $countries[0]['id'];
            }

            $countries = $this->call('res.country', 'search_read', [
                [['name', 'ilike', trim($countryName)]],
            ], [
                'fields' => ['id', 'name'],
                'limit' => 1,
            ]);

            return !empty($countries) && isset($countries[0]['id']) ? (int) $countries[0]['id'] : null;
        } catch (\Exception $e) {
            Log::warning('OdooClient: Error finding country', [
                'country' => $countryName,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Create a draft RENTAL Order in Odoo from a Laravel Order
     * 
     * ⚠️ IMPORTANTE: Este método crea órdenes de ALQUILER (Rental), no ventas normales.
     * 
     * Características:
     * - Crea sale.order marcada como is_rental_order = true
     * - Usa fechas de inicio/fin del JobLocation
     * - NO incluye precios manualmente (Odoo los calcula)
     * - Valida que productos sean alquilables (rent_ok = true)
     * - Queda en estado DRAFT para revisión manual
     * 
     * @param \App\Models\Order $order Laravel Order model
     * @return int Sale Order ID in Odoo
     * @throws \Exception If order data is invalid or creation fails
     */
    public function createDraftRentalOrder(\App\Models\Order $order): int
    {
        try {
            Log::info('🏗️ [ODOO] Creating draft RENTAL order', [
                'laravel_order_id' => $order->id,
                'order_status' => $order->status,
                'items_count' => $order->items->count(),
                'odoo_version' => $this->odooVersion,
            ]);

            $order->load(['items.product', 'job', 'user']);

            $billingDetails = $order->billing_details_json 
                ? json_decode($order->billing_details_json, true) 
                : null;

            if (empty($billingDetails) || empty($billingDetails['email'])) {
                throw new \InvalidArgumentException(
                    'Billing details with email are required to create a rental order'
                );
            }

            $partnerId = $this->findOrCreatePartner($billingDetails);
            $jobsiteInfo = $this->buildJobsiteInfo($order);
            $orderLines = $this->buildRentalOrderLines($order);

            $saleOrderData = [
                'partner_id' => $partnerId,
                'origin' => "Laravel Order #{$order->id}",
                'date_order' => $order->ordered_at 
                    ? $order->ordered_at->format('Y-m-d H:i:s') 
                    : now()->format('Y-m-d H:i:s'),
                'note' => $jobsiteInfo,
                'order_line' => $orderLines,
                'is_rental_order' => true,
            ];

            $rentalOrderTypeId = $this->findRentalOrderType();
            if ($rentalOrderTypeId) {
                $saleOrderData['type_id'] = $rentalOrderTypeId;
            }

            Log::debug('OdooClient: Rental order data prepared', [
                'partner_id' => $partnerId,
                'is_rental_order' => true,
                'lines_count' => count($orderLines),
            ]);

            $saleOrderId = $this->call('sale.order', 'create', [$saleOrderData]);

            if (empty($saleOrderId)) {
                throw new \Exception('Failed to create rental order in Odoo: No ID returned');
            }

            Log::info('✅ [ODOO] Draft RENTAL order created successfully', [
                'odoo_sale_order_id' => $saleOrderId,
                'laravel_order_id' => $order->id,
                'partner_id' => $partnerId,
            ]);

            return (int) $saleOrderId;

        } catch (\Exception $e) {
            Log::error('OdooClient: Error creating draft rental order', [
                'laravel_order_id' => $order->id ?? null,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Build RENTAL order lines for Odoo
     * 
     * ⚠️ REGLAS CRÍTICAS PARA RENTALS:
     * 
     * 1. NO incluir price_unit - Odoo lo calcula automáticamente
     * 2. Usar start_date y return_date (Odoo 16+) o reservation_begin/end (Odoo 15-)
     * 3. Validar que producto tenga rent_ok = true
     * 4. product_uom_qty = cantidad de equipos
     * 5. Odoo calcula precio basado en: días × tarifa diaria × cantidad
     * 
     * @param \App\Models\Order $order
     * @return array Order lines formatted for Odoo rental
     * @throws \Exception If product mapping or validation fails
     */
    protected function buildRentalOrderLines(\App\Models\Order $order): array
    {
        $productGroups = [];
        
        foreach ($order->items as $item) {
            $productId = $item->product_id;
            
            if (!isset($productGroups[$productId])) {
                $productGroups[$productId] = [
                    'product' => $item->product,
                    'items' => [],
                    'total_quantity' => 0,
                ];
            }
            
            $productGroups[$productId]['items'][] = $item;
            $productGroups[$productId]['total_quantity'] += $item->quantity;
        }

        $orderLines = [];

        if (!$order->job || !$order->job->date || !$order->job->end_date) {
            throw new \Exception(
                'Rental dates are required. Job must have both start date and end date.'
            );
        }

        $rentalStartDate = $order->job->date->format('Y-m-d H:i:s');
        $rentalEndDate = $order->job->end_date->format('Y-m-d H:i:s');
        $rentalDays = $order->job->date->diffInDays($order->job->end_date);

        Log::info('📅 [ODOO] Rental period', [
            'start_date' => $rentalStartDate,
            'end_date' => $rentalEndDate,
            'total_days' => $rentalDays,
        ]);

        foreach ($productGroups as $productId => $group) {
            $product = $group['product'];
            $totalQuantity = $group['total_quantity'];

            $odooProductId = $product->odoo_product_id;

            if (empty($odooProductId)) {
                throw new \Exception(
                    "Product '{$product->name}' (ID: {$product->id}) does not have an odoo_product_id mapped. " .
                    "Please map the product to Odoo before creating rental orders."
                );
            }

            // 🔥 VALIDACIÓN CRÍTICA: Verificar que el producto es alquilable
            try {
                $odooProduct = $this->call('product.product', 'read', [[(int) $odooProductId]], [
                    'fields' => ['id', 'name', 'rent_ok', 'active', 'list_price'],
                ]);

                if (empty($odooProduct)) {
                    throw new \Exception("Odoo product ID {$odooProductId} not found");
                }

                $odooProductData = $odooProduct[0];

                if (!$odooProductData['active']) {
                    throw new \Exception(
                        "Odoo product '{$odooProductData['name']}' (ID: {$odooProductId}) is archived"
                    );
                }

                if (!$odooProductData['rent_ok']) {
                    throw new \Exception(
                        "Odoo product '{$odooProductData['name']}' (ID: {$odooProductId}) is NOT configured for rental. " .
                        "Please enable 'Can be Rented' in Odoo product settings."
                    );
                }

                Log::debug('✅ [ODOO] Product validated for rental', [
                    'laravel_product_id' => $product->id,
                    'odoo_product_id' => $odooProductId,
                    'product_name' => $odooProductData['name'],
                    'rent_ok' => $odooProductData['rent_ok'],
                ]);

            } catch (\Exception $e) {
                Log::error('OdooClient: Product validation failed', [
                    'laravel_product_id' => $product->id,
                    'odoo_product_id' => $odooProductId,
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }

            $description = $product->name;
            if ($product->description) {
                $description .= "\n" . $product->description;
            }
            $description .= "\n📦 Equipment Quantity: {$totalQuantity} unit(s)";
            $description .= "\n📅 Rental Period: {$rentalDays} day(s)";

            $isOdoo16Plus = version_compare($this->odooVersion ?? '16.0', '16.0', '>=');

            $orderLine = [
                'product_id' => (int) $odooProductId,
                'product_uom_qty' => $totalQuantity,
                'name' => $description,
            ];

            if ($isOdoo16Plus || $this->odooVersion === 'unknown') {
                $orderLine['start_date'] = $rentalStartDate;
                $orderLine['return_date'] = $rentalEndDate;
            } else {
                $orderLine['reservation_begin'] = $rentalStartDate;
                $orderLine['reservation_end'] = $rentalEndDate;
            }

            Log::debug('📝 [ODOO] Building rental order line', [
                'product_name' => $product->name,
                'odoo_product_id' => $odooProductId,
                'equipment_quantity' => $totalQuantity,
                'rental_days' => $rentalDays,
            ]);

            $orderLines[] = [0, 0, $orderLine];
        }

        if (empty($orderLines)) {
            throw new \Exception('No rental order lines could be created.');
        }

        Log::info('✅ [ODOO] Rental order lines prepared', [
            'total_lines' => count($orderLines),
            'total_products' => count($productGroups),
            'rental_days' => $rentalDays,
        ]);

        return $orderLines;
    }

    /**
     * Find Rental order type ID (for Odoo 15 and earlier)
     * 
     * @return int|null
     */
    protected function findRentalOrderType(): ?int
    {
        try {
            $orderTypes = $this->call('sale.order.type', 'search_read', [
                ['|', ['name', '=', 'Rental'], ['name', '=', 'Alquiler']],
            ], [
                'fields' => ['id', 'name'],
                'limit' => 1,
            ]);
            
            if (!empty($orderTypes) && isset($orderTypes[0]['id'])) {
                Log::debug('OdooClient: Found rental order type', [
                    'type_id' => $orderTypes[0]['id'],
                    'type_name' => $orderTypes[0]['name'],
                ]);
                return (int) $orderTypes[0]['id'];
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::debug('OdooClient: sale.order.type model not available');
            return null;
        }
    }

    /**
     * Confirm a RENTAL Order in Odoo
     * 
     * @param int $saleOrderId Odoo Sale Order ID
     * @return bool True if confirmation successful
     * @throws \Exception If confirmation fails
     */
    public function confirmRentalOrder(int $saleOrderId): bool
    {
        try {
            Log::info('✅ [ODOO] Confirming RENTAL order', [
                'odoo_sale_order_id' => $saleOrderId,
            ]);

            $confirmed = false;
            
            try {
                $result = $this->call('sale.order', 'action_confirm_rental', [[$saleOrderId]]);
                $confirmed = true;
                Log::debug('OdooClient: Used action_confirm_rental method');
            } catch (\Exception $e) {
                Log::debug('OdooClient: action_confirm_rental not available, using standard confirmation');
            }

            if (!$confirmed) {
                $result = $this->call('sale.order', 'action_confirm', [[$saleOrderId]]);
            }

            $orderData = $this->call('sale.order', 'read', [[$saleOrderId]], [
                'fields' => ['id', 'name', 'state', 'is_rental_order', 'rental_status', 'amount_total'],
            ]);

            if (empty($orderData) || !isset($orderData[0])) {
                throw new \Exception('Failed to verify rental order confirmation');
            }

            $order = $orderData[0];
            $orderState = $order['state'] ?? null;

            if ($orderState !== 'sale' && $orderState !== 'sent') {
                throw new \Exception(
                    "Rental order confirmation failed. Current state: {$orderState}"
                );
            }

            Log::info('✅ [ODOO] RENTAL order confirmed successfully', [
                'odoo_sale_order_id' => $saleOrderId,
                'order_name' => $order['name'] ?? null,
                'state' => $orderState,
                'rental_status' => $order['rental_status'] ?? null,
                'amount_total' => $order['amount_total'] ?? 0,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('OdooClient: Error confirming rental order', [
                'odoo_sale_order_id' => $saleOrderId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Create and confirm a RENTAL Order in Odoo
     * 
     * @param \App\Models\Order $order Laravel Order model
     * @return int Sale Order ID in Odoo
     * @throws \Exception If creation or confirmation fails
     */
    public function createAndConfirmRentalOrder(\App\Models\Order $order): int
    {
        try {
            $saleOrderId = $this->createDraftRentalOrder($order);
            $this->confirmRentalOrder($saleOrderId);

            Log::info('✅ [ODOO] Rental order created and confirmed successfully', [
                'odoo_sale_order_id' => $saleOrderId,
                'laravel_order_id' => $order->id,
            ]);

            return $saleOrderId;

        } catch (\Exception $e) {
            Log::error('OdooClient: Error creating and confirming rental order', [
                'laravel_order_id' => $order->id ?? null,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Generate an invoice from a confirmed RENTAL Order
     * 
     * @param int $saleOrderId Odoo Sale Order ID (must be confirmed)
     * @return int Invoice ID (account.move ID) in Odoo
     * @throws \Exception If invoice creation fails
     */
    public function createInvoiceFromRentalOrder(int $saleOrderId): int
    {
        try {
            Log::info('📄 [ODOO] Creating invoice from RENTAL order', [
                'odoo_sale_order_id' => $saleOrderId,
            ]);

            $orderData = $this->call('sale.order', 'read', [[$saleOrderId]], [
                'fields' => ['id', 'name', 'state', 'is_rental_order', 'invoice_ids'],
            ]);

            if (empty($orderData) || !isset($orderData[0])) {
                throw new \Exception("Sale order {$saleOrderId} not found in Odoo");
            }

            $order = $orderData[0];
            $orderState = $order['state'] ?? null;

            if ($orderState !== 'sale' && $orderState !== 'sent') {
                throw new \Exception(
                    "Sale order {$saleOrderId} must be confirmed before creating invoice. Current state: {$orderState}"
                );
            }

            $existingInvoices = $order['invoice_ids'] ?? [];
            if (!empty($existingInvoices)) {
                Log::warning('OdooClient: Invoice already exists for rental order', [
                    'odoo_sale_order_id' => $saleOrderId,
                    'existing_invoice_ids' => $existingInvoices,
                ]);
                return (int) $existingInvoices[0];
            }

            $result = $this->call('sale.order', '_create_invoices', [[$saleOrderId]]);
            
            $invoiceId = null;
            
            if (is_array($result) && !empty($result)) {
                $invoiceId = (int) $result[0];
            } elseif (is_numeric($result) && $result > 0) {
                $orderDataAfter = $this->call('sale.order', 'read', [[$saleOrderId]], [
                    'fields' => ['invoice_ids'],
                ]);
                
                if (!empty($orderDataAfter[0]['invoice_ids'])) {
                    $invoiceId = (int) $orderDataAfter[0]['invoice_ids'][0];
                }
            }
            
            if (empty($invoiceId)) {
                throw new \Exception('Failed to create invoice from rental order');
            }

            $this->call('account.move', 'action_post', [[$invoiceId]]);

            $invoiceData = $this->call('account.move', 'read', [[$invoiceId]], [
                'fields' => ['id', 'name', 'state', 'amount_total', 'invoice_origin'],
            ]);

            if (empty($invoiceData) || !isset($invoiceData[0])) {
                throw new \Exception("Invoice {$invoiceId} was created but could not be retrieved");
            }

            $invoice = $invoiceData[0];

            Log::info('✅ [ODOO] Invoice created from RENTAL order', [
                'invoice_id' => $invoiceId,
                'invoice_name' => $invoice['name'] ?? null,
                'invoice_state' => $invoice['state'] ?? null,
                'amount_total' => $invoice['amount_total'] ?? null,
                'sale_order_id' => $saleOrderId,
            ]);

            return (int) $invoiceId;

        } catch (\Exception $e) {
            Log::error('OdooClient: Error creating invoice from rental order', [
                'odoo_sale_order_id' => $saleOrderId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Generate a payment link from an Odoo invoice
     * 
     * @param int $invoiceId Odoo Invoice ID (account.move)
     * @return string Payment link URL
     * @throws \Exception If payment link generation fails
     */
    public function generatePaymentLink(int $invoiceId): string
    {
        try {
            Log::info('🔗 [ODOO] Generating payment link for invoice', [
                'invoice_id' => $invoiceId,
            ]);

            $invoiceData = $this->call('account.move', 'read', [[$invoiceId]], [
                'fields' => ['id', 'name', 'state', 'amount_total', 'payment_state', 'partner_id', 'currency_id', 'amount_residual'],
            ]);

            if (empty($invoiceData) || !isset($invoiceData[0])) {
                throw new \Exception("Invoice {$invoiceId} not found in Odoo");
            }

            $invoice = $invoiceData[0];
            $partnerId = $invoice['partner_id'][0] ?? null;
            $currencyId = $invoice['currency_id'][0] ?? null;
            $amount = (float) ($invoice['amount_residual'] ?? $invoice['amount_total'] ?? 0);

            $paymentLink = null;
            
            try {
                $wizardData = [
                    'res_id' => $invoiceId,
                    'res_model' => 'account.move',
                    'amount' => $amount,
                ];

                if ($currencyId) {
                    $wizardData['currency_id'] = $currencyId;
                }

                if ($partnerId) {
                    $wizardData['partner_id'] = $partnerId;
                }

                $wizardId = $this->call('payment.link.wizard', 'create', [$wizardData]);

                if (!empty($wizardId)) {
                    $this->call('payment.link.wizard', 'action_generate_link', [[$wizardId]]);

                    $wizardResult = $this->call('payment.link.wizard', 'read', [[$wizardId]], [
                        'fields' => ['link'],
                    ]);

                    if (!empty($wizardResult) && isset($wizardResult[0]['link'])) {
                        $paymentLink = $wizardResult[0]['link'];
                    }
                }
            } catch (\Exception $e) {
                Log::debug('OdooClient: payment.link.wizard failed');
            }

            if (empty($paymentLink)) {
                try {
                    $invoiceAccess = $this->call('account.move', 'read', [[$invoiceId]], [
                        'fields' => ['access_token', 'name'],
                    ]);

                    if (!empty($invoiceAccess) && isset($invoiceAccess[0])) {
                        $accessToken = $invoiceAccess[0]['access_token'] ?? null;
                        $invoiceName = $invoiceAccess[0]['name'] ?? '';

                        if ($accessToken) {
                            $paymentLink = rtrim($this->url, '/') . '/my/invoices/' . $invoiceId . '?access_token=' . $accessToken;
                        } else {
                            $paymentLink = rtrim($this->url, '/') . '/payment/pay?' . http_build_query([
                                'reference' => $invoiceName,
                                'amount' => $amount,
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('OdooClient: Failed to construct payment link manually');
                }
            }

            if (empty($paymentLink)) {
                throw new \Exception('Failed to generate payment link');
            }

            Log::info('✅ [ODOO] Payment link generated', [
                'invoice_id' => $invoiceId,
                'invoice_name' => $invoice['name'] ?? null,
            ]);

            return $paymentLink;

        } catch (\Exception $e) {
            Log::error('OdooClient: Error generating payment link', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Send invoice email to customer using Odoo's templates
     * 
     * @param int $invoiceId Odoo Invoice ID (account.move)
     * @return bool True if email was sent successfully
     * @throws \Exception If email sending fails
     */
    public function sendInvoiceEmail(int $invoiceId): bool
    {
        try {
            Log::info('📧 [ODOO] Sending invoice email via Odoo', [
                'invoice_id' => $invoiceId,
            ]);

            $invoiceData = $this->call('account.move', 'read', [[$invoiceId]], [
                'fields' => ['id', 'name', 'state', 'partner_id'],
            ]);

            if (empty($invoiceData) || !isset($invoiceData[0])) {
                throw new \Exception("Invoice {$invoiceId} not found in Odoo");
            }

            $invoice = $invoiceData[0];
            $partnerId = $invoice['partner_id'][0] ?? null;

            if (empty($partnerId)) {
                throw new \Exception("Invoice {$invoiceId} has no partner assigned");
            }

            $partnerData = $this->call('res.partner', 'read', [[$partnerId]], [
                'fields' => ['id', 'name', 'email'],
            ]);

            if (empty($partnerData) || !isset($partnerData[0])) {
                throw new \Exception("Partner {$partnerId} not found");
            }

            $partnerEmail = $partnerData[0]['email'] ?? null;

            if (empty($partnerEmail)) {
                throw new \Exception("Partner {$partnerId} has no email address");
            }

            try {
                $action = $this->call('account.move', 'action_invoice_send', [[$invoiceId]]);

                $wizardId = null;

                if (is_array($action) && isset($action['res_id'])) {
                    $wizardId = (int) $action['res_id'];
                }

                if (!$wizardId) {
                    throw new \Exception('Invoice send wizard was not created');
                }

                $this->call('account.move.send', 'send_and_print', [[$wizardId]]);

                Log::info('✅ [ODOO] Invoice email sent successfully', [
                    'invoice_id' => $invoiceId,
                    'customer_email' => $partnerEmail,
                    'invoice_name' => $invoice['name'] ?? null,
                ]);

                return true;

            } catch (\Exception $e) {
                Log::error('OdooClient: Email sending failed', [
                    'invoice_id' => $invoiceId,
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('OdooClient: Error sending invoice email', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Build jobsite information from order
     * 
     * @param \App\Models\Order $order
     * @return string
     */
    protected function buildJobsiteInfo(\App\Models\Order $order): string
    {
        $info = [];

        if ($order->job && $order->job->notes) {
            $info[] = "Jobsite Address: " . $order->job->notes;
        }

        if ($order->job && $order->job->date) {
            $info[] = "Start Date: " . $order->job->date->format('Y-m-d');
        }

        if ($order->job && $order->job->end_date) {
            $info[] = "End Date: " . $order->job->end_date->format('Y-m-d');
        }

        if ($order->job && $order->job->latitude && $order->job->longitude) {
            $info[] = "Coordinates: {$order->job->latitude}, {$order->job->longitude}";
        }

        if ($order->notes) {
            $info[] = "Order Notes: " . $order->notes;
        }

        if ($order->foreman_details_json) {
            $foremanDetails = json_decode($order->foreman_details_json, true);
            if ($foremanDetails) {
                $foremanInfo = [];
                if (!empty($foremanDetails['firstName']) || !empty($foremanDetails['lastName'])) {
                    $foremanInfo[] = trim(($foremanDetails['firstName'] ?? '') . ' ' . ($foremanDetails['lastName'] ?? ''));
                }
                if (!empty($foremanDetails['phone'])) {
                    $foremanInfo[] = "Phone: " . $foremanDetails['phone'];
                }
                if (!empty($foremanDetails['email'])) {
                    $foremanInfo[] = "Email: " . $foremanDetails['email'];
                }
                if (!empty($foremanInfo)) {
                    $info[] = "Foreman: " . implode(', ', $foremanInfo);
                }
            }
        }

        return implode("\n", $info);
    }

    // ==================== MÉTODOS LEGACY (Deprecados pero conservados) ====================
    
    /**
     * @deprecated Use createDraftRentalOrder() instead
     * Conservado para compatibilidad con código legacy
     */
    public function createDraftSaleOrder(\App\Models\Order $order): int
    {
        Log::warning('OdooClient: createDraftSaleOrder() is deprecated. Use createDraftRentalOrder() for rental orders.');
        return $this->createDraftRentalOrder($order);
    }

    /**
     * @deprecated Use confirmRentalOrder() instead
     * Conservado para compatibilidad con código legacy
     */
    public function confirmSaleOrder(int $saleOrderId): bool
    {
        Log::warning('OdooClient: confirmSaleOrder() is deprecated. Use confirmRentalOrder() for rental orders.');
        return $this->confirmRentalOrder($saleOrderId);
    }

    /**
     * @deprecated Use createAndConfirmRentalOrder() instead
     * Conservado para compatibilidad con código legacy
     */
    public function createAndConfirmSaleOrder(\App\Models\Order $order): int
    {
        Log::warning('OdooClient: createAndConfirmSaleOrder() is deprecated. Use createAndConfirmRentalOrder() for rental orders.');
        return $this->createAndConfirmRentalOrder($order);
    }

    /**
     * @deprecated Use createInvoiceFromRentalOrder() instead
     * Conservado para compatibilidad con código legacy
     */
    public function createInvoiceFromSaleOrder(int $saleOrderId): int
    {
        Log::warning('OdooClient: createInvoiceFromSaleOrder() is deprecated. Use createInvoiceFromRentalOrder() for rental orders.');
        return $this->createInvoiceFromRentalOrder($saleOrderId);
    }

    // ==================== MÉTODOS HELPER (Comentados - NO eliminados) ====================
    
    /**
     * Find product in Odoo by name
     * 
     * 
     * ⚠️ MÉTODO CONSERVADO PERO NO USADO
     * Puede ser útil para scripts de migración o sincronización manual
     * 
     * @param string $productName
     * @return int|null Product ID or null if not found
     */
    /*
    protected function findProductByName(string $productName): ?int
    {
        try {
            $products = $this->call('product.product', 'search_read', [
                [['name', '=', trim($productName)]],
            ], [
                'fields' => ['id', 'name'],
                'limit' => 1,
            ]);

            if (!empty($products) && isset($products[0]['id'])) {
                return (int) $products[0]['id'];
            }

            return null;
        } catch (\Exception $e) {
            Log::warning('OdooClient: Error finding product by name', [
                'product_name' => $productName,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
    */
}