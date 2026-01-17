<?php

namespace App\Services\Odoo;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Odoo JSON-RPC Client
 * 
 * Reusable client for interacting with Odoo ERP via JSON-RPC 2.0.
 * Handles authentication, error management, and provides a generic interface
 * for calling any Odoo model and method.
 */
class OdooClient
{
    /**
     * Odoo base URL
     */
    protected string $url;

    /**
     * Odoo database name
     */
    protected string $database;

    /**
     * Odoo username
     */
    protected string $username;

    /**
     * Odoo API key (password)
     */
    protected string $apiKey;

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
                'verify' => false, // Disable SSL certificate verification
            ]);
        }
        
        return $client;
    }

    /**
     * Create a new Odoo client instance
     */
    public function __construct()
    {
        $this->url = rtrim(config('services.odoo.url', ''), '/');
        $this->database = config('services.odoo.database', '');
        $this->username = config('services.odoo.username', '');
        $this->apiKey = config('services.odoo.api_key', '');

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
            Log::info('🔐 [ODOO] Using cached authentication', [
                'uid' => $this->uid,
                'database' => $this->database,
            ]);
            return true;
        }

        try {
            Log::info('🔐 [ODOO] Starting authentication', [
                'url' => $this->url,
                'database' => $this->database,
                'username' => $this->username,
            ]);

            // Authenticate using JSON-RPC common endpoint
            // Odoo uses /jsonrpc endpoint with authenticate method
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

            $response = $this->getHttpClient(30)
                ->post($this->url . '/jsonrpc', $payload);

            if (!$response->successful()) {
                throw new \Exception('Odoo authentication failed: HTTP ' . $response->status());
            }

            $data = $response->json();

            // Check for JSON-RPC error
            if (isset($data['error'])) {
                Log::error('OdooClient: Authentication error', [
                    'error' => $data['error'],
                ]);
                throw new \Exception('Odoo authentication failed: ' . ($data['error']['message'] ?? 'Unknown error'));
            }

            // Extract uid from result
            if (isset($data['result']) && $data['result'] !== false) {
                $this->uid = (int) $data['result'];

                // Cache authentication data
                Cache::put($this->cacheKey, [
                    'uid' => $this->uid,
                    'session_id' => null, // Not used in JSON-RPC
                ], $this->authCacheTtl);

                Log::info('✅ [ODOO] Authentication successful', [
                    'uid' => $this->uid,
                    'database' => $this->database,
                    'username' => $this->username,
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
     * Call an Odoo model method
     * 
     * @param string $model Odoo model name (e.g., 'sale.order', 'res.partner')
     * @param string $method Method to call (e.g., 'create', 'write', 'search_read')
     * @param array $args Arguments for the method
     * @param array $kwargs Optional keyword arguments
     * @return mixed Response from Odoo
     * @throws \Exception If the call fails
     */
    public function call(string $model, string $method, array $args = [], array $kwargs = [])
    {
        // Ensure we're authenticated
        if ($this->uid === null) {
            $this->authenticate();
        }

        try {
            // Build JSON-RPC request
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
                'uid' => $this->uid,
            ]);

            // Make the request
            $response = $this->getHttpClient(60)
                ->post($this->url . '/jsonrpc', $payload);

            if (!$response->successful()) {
                throw new \Exception('Odoo RPC call failed: HTTP ' . $response->status());
            }

            $data = $response->json();

            // Check for JSON-RPC error
            if (isset($data['error'])) {
                $errorMessage = $data['error']['message'] ?? 'Unknown error';
                $errorCode = $data['error']['code'] ?? null;
                $errorData = $data['error']['data'] ?? null;

                Log::error('OdooClient: RPC call error', [
                    'model' => $model,
                    'method' => $method,
                    'error_code' => $errorCode,
                    'error_message' => $errorMessage,
                    'error_data' => $errorData,
                ]);

                throw new \Exception("Odoo error ({$errorCode}): {$errorMessage}", $errorCode ?? 0);
            }

            // Return result
            if (isset($data['result'])) {
                Log::debug('✅ [ODOO] RPC call successful', [
                    'model' => $model,
                    'method' => $method,
                    'request_id' => $requestId,
                    'result_type' => gettype($data['result']),
                    'result_is_array' => is_array($data['result']),
                    'result_count' => is_array($data['result']) ? count($data['result']) : null,
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

            // If authentication error, clear cache and retry once
            if (str_contains($e->getMessage(), 'authentication') || str_contains($e->getMessage(), 'session')) {
                Cache::forget($this->cacheKey);
                $this->uid = null;
                $this->sessionId = null;
                
                // Retry authentication and call
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
     * Searches for a partner by email. If found, returns the existing partner ID.
     * If not found, creates a new partner with the provided billing details.
     * 
     * @param array $billingDetails Billing details from Laravel checkout form
     *   Expected keys:
     *   - email (required): Email address to search/create
     *   - firstName: First name
     *   - lastName: Last name
     *   - phone: Phone number
     *   - addressLine1: Street address line 1
     *   - addressLine2: Street address line 2 (optional)
     *   - city: City name
     *   - state: State/Province name
     *   - zip: ZIP/Postal code
     *   - country: Country name
     *   - isCompany: Boolean indicating if this is a company
     *   - companyName: Company name (if isCompany is true)
     *   - jobTitle: Job title (optional)
     * 
     * @return int Partner ID in Odoo
     * @throws \Exception If email is missing or creation fails
     */
    public function findOrCreatePartner(array $billingDetails): int
    {
        // Validate required email
        if (empty($billingDetails['email'])) {
            throw new \InvalidArgumentException('Email is required to find or create a partner');
        }

        $email = trim($billingDetails['email']);

        try {
            // Search for existing partner by email
            Log::info('👤 [ODOO] Searching for partner by email', [
                'email' => $email,
            ]);
            
            $existingPartners = $this->call('res.partner', 'search_read', [
                [['email', '=', $email]],
            ], [
                'fields' => ['id', 'name', 'email', 'is_company'],
                'limit' => 1,
            ]);

            // If partner found, return the ID
            if (!empty($existingPartners) && isset($existingPartners[0]['id'])) {
                $partnerId = (int) $existingPartners[0]['id'];
                Log::info('✅ [ODOO] Found existing partner', [
                    'partner_id' => $partnerId,
                    'email' => $email,
                    'note' => 'Reusing existing partner in Odoo',
                ]);
                return $partnerId;
            }

            // Partner not found, create a new one
            Log::info('➕ [ODOO] Partner not found, creating new partner', [
                'email' => $email,
            ]);

            // Map Laravel billing fields to Odoo partner fields
            $partnerData = $this->mapBillingToPartner($billingDetails);

            // Create the partner
            $partnerId = $this->call('res.partner', 'create', [$partnerData]);

            if (empty($partnerId)) {
                throw new \Exception('Failed to create partner in Odoo: No ID returned');
            }

            Log::info('✅ [ODOO] Partner created successfully', [
                'partner_id' => $partnerId,
                'email' => $email,
                'is_company' => $partnerData['is_company'] ?? false,
                'note' => 'New partner created in Odoo',
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
     * @param array $billingDetails Billing details from Laravel
     * @return array Partner data formatted for Odoo
     */
    protected function mapBillingToPartner(array $billingDetails): array
    {
        $partnerData = [];

        // Name: Use company name if company, otherwise first + last name
        if (!empty($billingDetails['isCompany']) && !empty($billingDetails['companyName'])) {
            $partnerData['name'] = trim($billingDetails['companyName']);
            $partnerData['is_company'] = true;
        } else {
            $firstName = trim($billingDetails['firstName'] ?? '');
            $lastName = trim($billingDetails['lastName'] ?? '');
            $partnerData['name'] = trim($firstName . ' ' . $lastName);
            $partnerData['is_company'] = false;
        }

        // Email (required)
        if (!empty($billingDetails['email'])) {
            $partnerData['email'] = trim($billingDetails['email']);
        }

        // Phone
        if (!empty($billingDetails['phone'])) {
            $partnerData['phone'] = trim($billingDetails['phone']);
        }

        // Address fields
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

        // State: Try to find state by name and set state_id
        if (!empty($billingDetails['state'])) {
            $stateId = $this->findStateId($billingDetails['state']);
            if ($stateId) {
                $partnerData['state_id'] = $stateId;
            } else {
                // If state not found, store as string in comment or custom field
                // Odoo may have a 'comment' field for notes
                Log::warning('OdooClient: State not found in Odoo', [
                    'state' => $billingDetails['state'],
                ]);
            }
        }

        // Country: Try to find country by name and set country_id
        if (!empty($billingDetails['country'])) {
            $countryId = $this->findCountryId($billingDetails['country']);
            if ($countryId) {
                $partnerData['country_id'] = $countryId;
            } else {
                Log::warning('OdooClient: Country not found in Odoo', [
                    'country' => $billingDetails['country'],
                ]);
            }
        }

        // Job title (function field in Odoo)
        if (!empty($billingDetails['jobTitle'])) {
            $partnerData['function'] = trim($billingDetails['jobTitle']);
        }

        // Customer type: Set as customer by default
        $partnerData['customer_rank'] = 1;

        Log::debug('OdooClient: Mapped billing details to partner data', [
            'partner_data' => $partnerData,
        ]);

        return $partnerData;
    }

    /**
     * Find Odoo state ID by name
     * 
     * @param string $stateName State name
     * @return int|null State ID or null if not found
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

            if (!empty($states) && isset($states[0]['id'])) {
                return (int) $states[0]['id'];
            }

            return null;
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
     * @param string $countryName Country name
     * @return int|null Country ID or null if not found
     */
    protected function findCountryId(string $countryName): ?int
    {
        try {
            // Try exact match first
            $countries = $this->call('res.country', 'search_read', [
                [['name', '=', trim($countryName)]],
            ], [
                'fields' => ['id', 'name'],
                'limit' => 1,
            ]);

            if (!empty($countries) && isset($countries[0]['id'])) {
                return (int) $countries[0]['id'];
            }

            // Try case-insensitive match
            $countries = $this->call('res.country', 'search_read', [
                [['name', 'ilike', trim($countryName)]],
            ], [
                'fields' => ['id', 'name'],
                'limit' => 1,
            ]);

            if (!empty($countries) && isset($countries[0]['id'])) {
                return (int) $countries[0]['id'];
            }

            return null;
        } catch (\Exception $e) {
            Log::warning('OdooClient: Error finding country', [
                'country' => $countryName,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Create a draft Sale Order in Odoo from a Laravel Order
     * 
     * Creates a sale.order in Odoo in draft state with:
     * - Partner (customer) from billing details
     * - Laravel order ID in origin field
     * - Jobsite address and notes
     * - Order lines from Laravel order items
     * 
     * The order is NOT confirmed - it remains in draft state for manual review.
     * 
     * @param \App\Models\Order $order Laravel Order model
     * @return int Sale Order ID in Odoo
     * @throws \Exception If order data is invalid or creation fails
     */
    public function createDraftSaleOrder(\App\Models\Order $order): int
    {
        try {
            Log::info('📦 [ODOO] Creating draft sale order', [
                'laravel_order_id' => $order->id,
                'order_status' => $order->status,
                'order_subtotal' => $order->subtotal,
                'items_count' => $order->items->count(),
            ]);

            // Load relationships
            $order->load(['items.product', 'job', 'user']);

            // Get or create partner from billing details
            $billingDetails = $order->billing_details_json 
                ? json_decode($order->billing_details_json, true) 
                : null;

            if (empty($billingDetails) || empty($billingDetails['email'])) {
                throw new \InvalidArgumentException(
                    'Billing details with email are required to create a sale order'
                );
            }

            $partnerId = $this->findOrCreatePartner($billingDetails);

            // Build jobsite address and notes
            $jobsiteInfo = $this->buildJobsiteInfo($order);

            // Build order lines
            $orderLines = $this->buildOrderLines($order);

            // Create sale order data
            $saleOrderData = [
                'partner_id' => $partnerId,
                'origin' => "Laravel Order #{$order->id}",
                'date_order' => $order->ordered_at ? $order->ordered_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                'note' => $jobsiteInfo,
                'order_line' => $orderLines,
                // State is 'draft' by default in Odoo, but we can explicitly set it
                // Note: We do NOT confirm the order - it remains in draft
            ];

            Log::debug('OdooClient: Sale order data prepared', [
                'partner_id' => $partnerId,
                'origin' => $saleOrderData['origin'],
                'lines_count' => count($orderLines),
            ]);

            // Create the sale order in Odoo
            $saleOrderId = $this->call('sale.order', 'create', [$saleOrderData]);

            if (empty($saleOrderId)) {
                throw new \Exception('Failed to create sale order in Odoo: No ID returned');
            }

            Log::info('✅ [ODOO] Draft sale order created successfully', [
                'odoo_sale_order_id' => $saleOrderId,
                'laravel_order_id' => $order->id,
                'partner_id' => $partnerId,
                'order_lines_count' => count($orderLines),
            ]);

            return (int) $saleOrderId;

        } catch (\Exception $e) {
            Log::error('OdooClient: Error creating draft sale order', [
                'laravel_order_id' => $order->id ?? null,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Confirm a Sale Order in Odoo
     * 
     * Confirms a sale.order in Odoo, which:
     * - Triggers tax calculation
     * - Prepares the order for invoicing
     * - Changes state from 'draft' to 'sale'
     * 
     * Note: This does NOT collect payment in Laravel.
     * All payment processing is handled in Odoo.
     * 
     * @param int $saleOrderId Odoo Sale Order ID
     * @return bool True if confirmation successful
     * @throws \Exception If confirmation fails
     */
    public function confirmSaleOrder(int $saleOrderId): bool
    {
        try {
            Log::info('✅ [ODOO] Confirming sale order', [
                'odoo_sale_order_id' => $saleOrderId,
            ]);

            // Call action_confirm on the sale order
            // This triggers tax calculation and prepares the order for invoicing
            $result = $this->call('sale.order', 'action_confirm', [[$saleOrderId]]);

            // Verify the order was confirmed by checking its state
            $orderData = $this->call('sale.order', 'read', [[$saleOrderId]], [
                'fields' => ['id', 'name', 'state'],
            ]);

            if (empty($orderData) || !isset($orderData[0])) {
                throw new \Exception('Failed to verify sale order confirmation: Order not found');
            }

            $orderState = $orderData[0]['state'] ?? null;

            // Check if order is confirmed (state should be 'sale')
            if ($orderState !== 'sale') {
                Log::warning('OdooClient: Sale order confirmation may have failed', [
                    'odoo_sale_order_id' => $saleOrderId,
                    'current_state' => $orderState,
                    'expected_state' => 'sale',
                ]);
                // Still return true if state is 'sent' (some Odoo versions use this)
                if ($orderState !== 'sent') {
                    throw new \Exception(
                        "Sale order confirmation failed. Current state: {$orderState}, expected: 'sale'"
                    );
                }
            }

            Log::info('✅ [ODOO] Sale order confirmed successfully', [
                'odoo_sale_order_id' => $saleOrderId,
                'order_name' => $orderData[0]['name'] ?? null,
                'state' => $orderState,
                'note' => 'Tax calculation triggered, order ready for invoicing',
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('OdooClient: Error confirming sale order', [
                'odoo_sale_order_id' => $saleOrderId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Create and confirm a Sale Order in Odoo from a Laravel Order
     * 
     * Creates a sale.order in Odoo and immediately confirms it.
     * This is a convenience method that combines createDraftSaleOrder() and confirmSaleOrder().
     * 
     * After confirmation:
     * - Tax calculation is triggered
     * - Order is prepared for invoicing
     * - Payment is NOT collected in Laravel (handled in Odoo)
     * 
     * @param \App\Models\Order $order Laravel Order model
     * @return int Sale Order ID in Odoo
     * @throws \Exception If creation or confirmation fails
     */
    public function createAndConfirmSaleOrder(\App\Models\Order $order): int
    {
        try {
            // Create draft sale order
            $saleOrderId = $this->createDraftSaleOrder($order);

            // Confirm the sale order
            $this->confirmSaleOrder($saleOrderId);

            Log::info('OdooClient: Sale order created and confirmed successfully', [
                'odoo_sale_order_id' => $saleOrderId,
                'laravel_order_id' => $order->id,
            ]);

            return $saleOrderId;

        } catch (\Exception $e) {
            Log::error('OdooClient: Error creating and confirming sale order', [
                'laravel_order_id' => $order->id ?? null,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Generate an invoice from a confirmed Sale Order in Odoo
     * 
     * Creates an invoice (account.move) from a confirmed sale.order.
     * The invoice:
     * - Includes all taxes calculated from the sale order
     * - Is linked to the sale order
     * - Is left unpaid (draft or posted state, not paid)
     * 
     * Note: The sale order must be confirmed before creating an invoice.
     * 
     * @param int $saleOrderId Odoo Sale Order ID (must be confirmed)
     * @return int Invoice ID (account.move ID) in Odoo
     * @throws \Exception If invoice creation fails or sale order is not confirmed
     */
    public function createInvoiceFromSaleOrder(int $saleOrderId): int
    {
        try {
            Log::info('📄 [ODOO] Creating invoice from sale order', [
                'odoo_sale_order_id' => $saleOrderId,
            ]);

            // Verify the sale order exists and is confirmed
            $orderData = $this->call('sale.order', 'read', [[$saleOrderId]], [
                'fields' => ['id', 'name', 'state', 'invoice_ids', 'invoice_count'],
            ]);

            if (empty($orderData) || !isset($orderData[0])) {
                throw new \Exception("Sale order {$saleOrderId} not found in Odoo");
            }

            $orderState = $orderData[0]['state'] ?? null;
            if ($orderState !== 'sale' && $orderState !== 'sent') {
                throw new \Exception(
                    "Sale order {$saleOrderId} must be confirmed before creating invoice. Current state: {$orderState}"
                );
            }

            // Check if invoice already exists
            $existingInvoices = $orderData[0]['invoice_ids'] ?? [];
            if (!empty($existingInvoices)) {
                Log::warning('OdooClient: Invoice already exists for sale order', [
                    'odoo_sale_order_id' => $saleOrderId,
                    'existing_invoice_ids' => $existingInvoices,
                ]);
                // Return the first existing invoice ID
                return (int) $existingInvoices[0];
            }

            // Create invoice from sale order
            // Use action_invoice_create (public method) instead of _create_invoices (private)
            // This method may return a wizard ID or directly create invoices depending on Odoo version
            $result = $this->call('sale.order', 'action_invoice_create', [[$saleOrderId]]);
            
            // action_invoice_create may return:
            // - A dictionary with 'res_id' (invoice ID) and 'res_model' (account.move)
            // - A list of invoice IDs
            // - A wizard ID (in older versions)
            // - True/False (in some versions)
            
            $invoiceId = null;
            
            if (is_array($result)) {
                // If it's an array, it might be a list of invoice IDs or a dictionary
                if (isset($result['res_id']) && isset($result['res_model']) && $result['res_model'] === 'account.move') {
                    // Dictionary format: {'res_id': invoice_id, 'res_model': 'account.move'}
                    $invoiceId = (int) $result['res_id'];
                } elseif (isset($result[0]) && is_numeric($result[0])) {
                    // List of invoice IDs
                    $invoiceId = (int) $result[0];
                } elseif (count($result) > 0 && is_numeric($result[0])) {
                    // Array of invoice IDs
                    $invoiceId = (int) $result[0];
                }
            } elseif (is_numeric($result) && $result > 0) {
                // Might be a wizard ID - try to read the invoice from the sale order
                // First, check if invoice was created by reading the sale order again
                $orderDataAfter = $this->call('sale.order', 'read', [[$saleOrderId]], [
                    'fields' => ['invoice_ids'],
                ]);
                
                if (!empty($orderDataAfter) && isset($orderDataAfter[0]['invoice_ids']) && !empty($orderDataAfter[0]['invoice_ids'])) {
                    $invoiceId = (int) $orderDataAfter[0]['invoice_ids'][0];
                } else {
                    // If no invoice found, the result might be a wizard ID
                    // In this case, we need to confirm the wizard (if applicable)
                    // For now, throw an error asking to check Odoo manually
                    throw new \Exception(
                        "action_invoice_create returned a wizard ID ({$result}). " .
                        "Invoice creation may require manual confirmation in Odoo. " .
                        "Please check the sale order in Odoo and create invoice manually if needed."
                    );
                }
            } elseif ($result === true) {
                // If it returns true, read the invoice from the sale order
                $orderDataAfter = $this->call('sale.order', 'read', [[$saleOrderId]], [
                    'fields' => ['invoice_ids'],
                ]);
                
                if (!empty($orderDataAfter) && isset($orderDataAfter[0]['invoice_ids']) && !empty($orderDataAfter[0]['invoice_ids'])) {
                    $invoiceId = (int) $orderDataAfter[0]['invoice_ids'][0];
                } else {
                    throw new \Exception('action_invoice_create returned true but no invoice was found');
                }
            }
            
            if (empty($invoiceId)) {
                // Last attempt: read the sale order to check if invoice was created
                $orderDataAfter = $this->call('sale.order', 'read', [[$saleOrderId]], [
                    'fields' => ['invoice_ids'],
                ]);
                
                if (!empty($orderDataAfter) && isset($orderDataAfter[0]['invoice_ids']) && !empty($orderDataAfter[0]['invoice_ids'])) {
                    $invoiceId = (int) $orderDataAfter[0]['invoice_ids'][0];
                } else {
                    throw new \Exception(
                        'Failed to create invoice: action_invoice_create did not return a valid invoice ID. ' .
                        "Result: " . json_encode($result) . ". " .
                        "Please check the sale order in Odoo and create invoice manually if needed."
                    );
                }
            }

            // Verify the invoice was created and get its details
            $invoiceData = $this->call('account.move', 'read', [[$invoiceId]], [
                'fields' => ['id', 'name', 'state', 'amount_total', 'invoice_line_ids', 'invoice_origin'],
            ]);

            if (empty($invoiceData) || !isset($invoiceData[0])) {
                throw new \Exception("Invoice {$invoiceId} was created but could not be retrieved");
            }

            $invoice = $invoiceData[0];

            // Verify invoice is linked to sale order
            $invoiceOrigin = $invoice['invoice_origin'] ?? '';
            if (empty($invoiceOrigin) || !str_contains($invoiceOrigin, $orderData[0]['name'])) {
                Log::warning('OdooClient: Invoice origin may not match sale order', [
                    'invoice_id' => $invoiceId,
                    'invoice_origin' => $invoiceOrigin,
                    'sale_order_name' => $orderData[0]['name'] ?? null,
                ]);
            }

            // Verify invoice includes taxes (check invoice lines)
            $invoiceLineIds = $invoice['invoice_line_ids'] ?? [];
            if (empty($invoiceLineIds)) {
                throw new \Exception("Invoice {$invoiceId} was created but has no invoice lines");
            }

            // Read invoice lines to verify taxes
            $invoiceLines = $this->call('account.move.line', 'read', [$invoiceLineIds], [
                'fields' => ['id', 'tax_ids', 'price_subtotal', 'price_total'],
            ]);

            $hasTaxes = false;
            foreach ($invoiceLines as $line) {
                $taxIds = $line['tax_ids'] ?? [];
                if (!empty($taxIds)) {
                    $hasTaxes = true;
                    break;
                }
            }

            if (!$hasTaxes) {
                Log::warning('OdooClient: Invoice created but no taxes found on invoice lines', [
                    'invoice_id' => $invoiceId,
                    'sale_order_id' => $saleOrderId,
                ]);
                // Don't throw exception - taxes might be calculated at invoice validation
            }

            // Verify invoice is unpaid
            $invoiceState = $invoice['state'] ?? null;
            if ($invoiceState === 'paid') {
                Log::warning('OdooClient: Invoice was created in paid state', [
                    'invoice_id' => $invoiceId,
                    'state' => $invoiceState,
                ]);
            }
            
            $this->call('account.move', 'action_post', [[$invoiceId]]);

            $invoiceAfterPost = $this->call('account.move', 'read', [[$invoiceId]], [
                'fields' => ['state'],
            ]);
            
            $invoiceState = $invoiceAfterPost[0]['state'] ?? null;
            
            Log::info('✅ [ODOO] Invoice created successfully from sale order', [
                'invoice_id' => $invoiceId,
                'invoice_name' => $invoice['name'] ?? null,
                'invoice_state' => $invoiceState,
                'amount_total' => $invoice['amount_total'] ?? null,
                'sale_order_id' => $saleOrderId,
                'has_taxes' => $hasTaxes,
                'invoice_lines_count' => count($invoiceLineIds),
                'note' => 'Invoice is unpaid and ready for customer payment',
            ]);

            return (int) $invoiceId;

        } catch (\Exception $e) {
            Log::error('OdooClient: Error creating invoice from sale order', [
                'odoo_sale_order_id' => $saleOrderId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Generate a payment link from an Odoo invoice
     * 
     * Creates a payment link for the invoice that supports:
     * - Card payments
     * - ACH / Wire transfers (if enabled in Odoo)
     * 
     * The payment link is returned for logging purposes only.
     * It should NOT be exposed in the Laravel UI.
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

            // Verify the invoice exists
            $invoiceData = $this->call('account.move', 'read', [[$invoiceId]], [
                'fields' => ['id', 'name', 'state', 'amount_total', 'payment_state'],
            ]);

            if (empty($invoiceData) || !isset($invoiceData[0])) {
                throw new \Exception("Invoice {$invoiceId} not found in Odoo");
            }

            $invoice = $invoiceData[0];
            $invoiceState = $invoice['state'] ?? null;

            // Check if invoice is already paid
            $paymentState = $invoice['payment_state'] ?? null;
            if ($paymentState === 'paid') {
                Log::warning('OdooClient: Invoice is already paid, payment link may not be needed', [
                    'invoice_id' => $invoiceId,
                    'payment_state' => $paymentState,
                ]);
            }

            // Get invoice details including partner and currency
            $invoiceDetails = $this->call('account.move', 'read', [[$invoiceId]], [
                'fields' => ['id', 'name', 'partner_id', 'currency_id', 'amount_total', 'amount_residual'],
            ]);

            if (empty($invoiceDetails) || !isset($invoiceDetails[0])) {
                throw new \Exception('Failed to retrieve invoice details');
            }

            $invoiceDetail = $invoiceDetails[0];
            $partnerId = $invoiceDetail['partner_id'][0] ?? null;
            $currencyId = $invoiceDetail['currency_id'][0] ?? null;
            $amount = (float) ($invoiceDetail['amount_residual'] ?? $invoiceDetail['amount_total'] ?? 0);

            // Method 1: Try using payment.link.wizard (Odoo 14+)
            $paymentLink = null;
            
            try {
                // Create payment link wizard
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
                    // Call action_generate_link to generate the payment link
                    $this->call('payment.link.wizard', 'action_generate_link', [[$wizardId]]);

                    // Read the generated link
                    $wizardResult = $this->call('payment.link.wizard', 'read', [[$wizardId]], [
                        'fields' => ['link'],
                    ]);

                    if (!empty($wizardResult) && isset($wizardResult[0]['link'])) {
                        $paymentLink = $wizardResult[0]['link'];
                    }
                }
            } catch (\Exception $e) {
                Log::debug('OdooClient: payment.link.wizard method failed, trying alternatives', [
                    'error' => $e->getMessage(),
                ]);
            }

            // Method 2: Try _get_payment_url method (if available)
            if (empty($paymentLink)) {
                try {
                    $paymentLink = $this->call('account.move', '_get_payment_url', [[$invoiceId]]);
                } catch (\Exception $e) {
                    Log::debug('OdooClient: _get_payment_url method not available', [
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Method 3: Try to get payment link from invoice's payment_link field
            if (empty($paymentLink)) {
                try {
                    $invoiceWithLink = $this->call('account.move', 'read', [[$invoiceId]], [
                        'fields' => ['payment_link'],
                    ]);

                    if (!empty($invoiceWithLink) && isset($invoiceWithLink[0]['payment_link'])) {
                        $paymentLink = $invoiceWithLink[0]['payment_link'];
                    }
                } catch (\Exception $e) {
                    Log::debug('OdooClient: payment_link field not available', [
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Method 4: Construct payment link manually using Odoo's payment portal
            if (empty($paymentLink)) {
                // Get invoice access token for secure payment link
                try {
                    $invoiceAccess = $this->call('account.move', 'read', [[$invoiceId]], [
                        'fields' => ['access_token', 'name'],
                    ]);

                    if (!empty($invoiceAccess) && isset($invoiceAccess[0])) {
                        $accessToken = $invoiceAccess[0]['access_token'] ?? null;
                        $invoiceName = $invoiceAccess[0]['name'] ?? '';

                        if ($accessToken) {
                            // Construct payment link with access token
                            $paymentLink = rtrim($this->url, '/') . '/my/invoices/' . $invoiceId . '?access_token=' . $accessToken;
                        } else {
                            // Fallback: Construct basic payment link
                            $paymentLink = rtrim($this->url, '/') . '/payment/pay?' . http_build_query([
                                'reference' => $invoiceName,
                                'amount' => $amount,
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('OdooClient: Failed to construct payment link manually', [
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            if (empty($paymentLink)) {
                throw new \Exception('Failed to generate payment link: All methods failed');
            }

            // Log the payment link (for logging purposes only)
            Log::info('✅ [ODOO] Payment link generated successfully', [
                'invoice_id' => $invoiceId,
                'invoice_name' => $invoice['name'] ?? null,
                'amount_total' => $invoice['amount_total'] ?? null,
                'payment_link_length' => strlen($paymentLink),
                'payment_link_preview' => substr($paymentLink, 0, 50) . '...',
                'note' => 'Payment link is for logging purposes only. Do NOT expose in Laravel UI.',
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
     * Send invoice email to customer using Odoo's email templates
     * 
     * Triggers Odoo to send the invoice email to the customer.
     * The email includes the payment link automatically (handled by Odoo).
     * 
     * Important: Laravel does NOT send payment links.
     * All email sending is handled by Odoo using its email templates.
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

            // Verify the invoice exists and get customer email
            $invoiceData = $this->call('account.move', 'read', [[$invoiceId]], [
                'fields' => ['id', 'name', 'state', 'partner_id', 'email_from', 'invoice_sent'],
            ]);

            if (empty($invoiceData) || !isset($invoiceData[0])) {
                throw new \Exception("Invoice {$invoiceId} not found in Odoo");
            }

            $invoice = $invoiceData[0];
            $partnerId = $invoice['partner_id'][0] ?? null;

            if (empty($partnerId)) {
                throw new \Exception("Invoice {$invoiceId} has no partner (customer) assigned");
            }

            // Get partner email
            $partnerData = $this->call('res.partner', 'read', [[$partnerId]], [
                'fields' => ['id', 'name', 'email'],
            ]);

            if (empty($partnerData) || !isset($partnerData[0])) {
                throw new \Exception("Partner {$partnerId} not found in Odoo");
            }

            $partnerEmail = $partnerData[0]['email'] ?? null;

            if (empty($partnerEmail)) {
                throw new \Exception("Partner {$partnerId} has no email address");
            }

            // Method 1: Use action_invoice_send (Odoo standard method)
            // This uses Odoo's email templates and includes payment link automatically
            try {


// 2️⃣ Crear wizard de envío
$action = $this->call('account.move', 'action_invoice_send', [[$invoiceId]]);

// 3️⃣ Extraer wizard ID correctamente
$wizardId = null;

if (is_array($action) && isset($action['res_id'])) {
    $wizardId = (int) $action['res_id'];
}

if (!$wizardId) {
    throw new \Exception('Invoice send wizard was not created');
}

// 4️⃣ Enviar email
$this->call('account.move.send', 'send_and_print', [[$wizardId]]);


                Log::info('✅ [ODOO] Invoice email sent successfully via action_invoice_send', [
                    'invoice_id' => $invoiceId,
                    'customer_email' => $partnerEmail,
                    'invoice_name' => $invoice['name'] ?? null,
                    'note' => 'Email includes payment link automatically via Odoo templates',
                ]);

                return true;

            } catch (\Exception $e) {
                Log::debug('OdooClient: action_invoice_send failed, trying alternative method', [
                    'error' => $e->getMessage(),
                ]);

                // Method 2: Use message_post_with_template
                // This sends email using a specific template
                try {
                    // Find the default invoice email template
                    $template = $this->call('mail.template', 'search_read', [
                        [['model', '=', 'account.move'], ['name', 'ilike', 'invoice']],
                    ], [
                        'fields' => ['id', 'name'],
                        'limit' => 1,
                    ]);

                    $templateId = null;
                    if (!empty($template) && isset($template[0]['id'])) {
                        $templateId = (int) $template[0]['id'];
                    }

                    // Send email using message_post_with_template
                    $emailValues = [
                        'email_from' => $partnerEmail, // Will be overridden by Odoo
                        'email_to' => $partnerEmail,
                        'subject' => "Invoice {$invoice['name']}",
                    ];

                    if ($templateId) {
                        $this->call('account.move', 'message_post_with_template', [
                            [$invoiceId],
                            $templateId,
                        ]);
                    } else {
                        // Fallback: Use message_post directly
                        $this->call('account.move', 'message_post', [
                            [$invoiceId],
                            [
                                'body' => "Invoice {$invoice['name']} has been sent.",
                                'subject' => "Invoice {$invoice['name']}",
                                'partner_ids' => [[6, 0, [$partnerId]]],
                                'email_from' => null, // Odoo will use system email
                            ],
                        ]);
                    }

                    Log::info('✅ [ODOO] Invoice email sent successfully via message_post', [
                        'invoice_id' => $invoiceId,
                        'customer_email' => $partnerEmail,
                        'template_id' => $templateId,
                        'note' => 'Email sent using Odoo email template',
                    ]);

                    return true;

                } catch (\Exception $e2) {
                    Log::error('OdooClient: All email sending methods failed', [
                        'invoice_id' => $invoiceId,
                        'action_invoice_send_error' => $e->getMessage(),
                        'message_post_error' => $e2->getMessage(),
                    ]);
                    throw new \Exception(
                        "Failed to send invoice email. Tried action_invoice_send and message_post. " .
                        "Last error: {$e2->getMessage()}"
                    );
                }
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
     * Build jobsite information string from order
     * 
     * @param \App\Models\Order $order
     * @return string Jobsite information formatted for Odoo notes
     */
    protected function buildJobsiteInfo(\App\Models\Order $order): string
    {
        $info = [];

        // Add jobsite address
        if ($order->job && $order->job->notes) {
            $info[] = "Jobsite Address: " . $order->job->notes;
        }

        // Add dates
        if ($order->job && $order->job->date) {
            $startDate = $order->job->date->format('Y-m-d');
            $info[] = "Start Date: {$startDate}";
        }

        if ($order->job && $order->job->end_date) {
            $endDate = $order->job->end_date->format('Y-m-d');
            $info[] = "End Date: {$endDate}";
        }

        // Add coordinates if available
        if ($order->job && $order->job->latitude && $order->job->longitude) {
            $info[] = "Coordinates: {$order->job->latitude}, {$order->job->longitude}";
        }

        // Add order notes
        if ($order->notes) {
            $info[] = "Order Notes: " . $order->notes;
        }

        // Add foreman details if available
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
                    $info[] = "Foreman/Receiving Person: " . implode(', ', $foremanInfo);
                }
            }
        }

        return implode("\n", $info);
    }

    /**
     * Build order lines for Odoo sale order
     * 
     * Creates rental order lines from Laravel order items.
     * Rules:
     * - Uses mapped odoo_product_id from Laravel products
     * - Quantity (product_uom_qty) reflects number of equipment units
     * - Price (price_unit) is daily rental price
     * - Rental dates (rental_start_date, rental_end_date) from JobLocation
     * - One line per product (items of same product are grouped)
     * 
     * @param \App\Models\Order $order
     * @return array Order lines formatted for Odoo rental
     */
    protected function buildOrderLines(\App\Models\Order $order): array
    {
        // Group items by product to create one line per product
        $productGroups = [];
        
        foreach ($order->items as $item) {
            $productId = $item->product_id;
            
            if (!isset($productGroups[$productId])) {
                $productGroups[$productId] = [
                    'product' => $item->product,
                    'items' => [],
                    'total_quantity' => 0,
                    'total_line_total' => 0,
                ];
            }
            
            $productGroups[$productId]['items'][] = $item;
            $productGroups[$productId]['total_quantity'] += $item->quantity;
            $productGroups[$productId]['total_line_total'] += $item->line_total;
        }

        $orderLines = [];

        foreach ($productGroups as $productId => $group) {
            $product = $group['product'];
            $items = $group['items'];
            $totalQuantity = $group['total_quantity'];
            $totalLineTotal = $group['total_line_total'];

            // Get Odoo product ID from mapped field
            $odooProductId = $product->odoo_product_id;

            if (empty($odooProductId)) {
                Log::warning('OdooClient: Product missing odoo_product_id', [
                    'laravel_product_id' => $product->id,
                    'product_name' => $product->name,
                ]);
                throw new \Exception(
                    "Product '{$product->name}' (ID: {$product->id}) does not have an odoo_product_id mapped. " .
                    "Please map the product to Odoo before creating sale orders."
                );
            }

            // Get unit price from first item (all items of same product have same price)
            $unitPrice = $items[0]->unit_price;

            // Build product description with rental information
            $description = $product->name;
            if ($product->description) {
                $description .= "\n" . $product->description;
            }
            $description .= "\nEquipment Quantity: {$totalQuantity} unit(s)";

            // Get rental dates from JobLocation
            $rentalStartDate = $order->job->date->format('Y-m-d H:i:s');
            $rentalEndDate = $order->job->end_date->format('Y-m-d H:i:s');

            // Create order line for rental
            // - product_uom_qty: number of equipment units
            // - price_unit: daily rental price
            // - product_id: mapped Odoo product ID
            // - is_rental: marks this as a rental order line
            // - rental_start_date: start date of rental period
            // - rental_end_date: end date of rental period
            $orderLine = [
                'product_id' => (int) $odooProductId,
                'product_uom_qty' => $totalQuantity, // Number of equipment units
                'price_unit' => (float) $unitPrice, // Daily rental price
                'name' => $description,
                'is_rental' => true,
                'rental_start_date' => $rentalStartDate,
                'rental_end_date' => $rentalEndDate,
            ];

            Log::debug('📝 [ODOO] Building rental order line', [
                'laravel_product_id' => $product->id,
                'product_name' => $product->name,
                'odoo_product_id' => $odooProductId,
                'equipment_quantity' => $totalQuantity,
                'daily_price' => $unitPrice,
                'line_total' => $totalLineTotal,
                'rental_start_date' => $rentalStartDate,
                'rental_end_date' => $rentalEndDate,
            ]);

            $orderLines[] = [0, 0, $orderLine]; // Odoo format: [0, 0, values] for create
        }

        return $orderLines;
    }

    /**
     * Find product in Odoo by name
     * 
     * @param string $productName
     * @return int|null Product ID or null if not found
     */
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

    /**
     * Find or create service product category in Odoo
     * 
     * @return int Category ID
     */
    protected function findOrCreateServiceCategory(): int
    {
        try {
            // Try to find existing "Services" category
            $categories = $this->call('product.category', 'search_read', [
                [['name', '=', 'Services']],
            ], [
                'fields' => ['id'],
                'limit' => 1,
            ]);

            if (!empty($categories) && isset($categories[0]['id'])) {
                return (int) $categories[0]['id'];
            }

            // Create category if not found
            $categoryId = $this->call('product.category', 'create', [[
                'name' => 'Services',
            ]]);

            return (int) $categoryId;
        } catch (\Exception $e) {
            Log::warning('OdooClient: Error finding/creating service category', [
                'error' => $e->getMessage(),
            ]);
            // Return a default category or handle error
            throw new \Exception('Failed to find or create service category: ' . $e->getMessage());
        }
    }

    /**
     * Create a service product in Odoo for rental items
     * 
     * @param \App\Models\Product $product Laravel product
     * @param int $categoryId Odoo category ID
     * @return int Product ID in Odoo
     */
    protected function createServiceProduct(\App\Models\Product $product, int $categoryId): int
    {
        try {
            // Find or create product template
            $templateId = $this->call('product.template', 'create', [[
                'name' => $product->name,
                'description' => $product->description ?? '',
                'categ_id' => $categoryId,
                'type' => 'service', // Service type for rentals
                'sale_ok' => true,
                'purchase_ok' => false,
                'list_price' => (float) $product->price,
            ]]);

            // Get the product variant
            $products = $this->call('product.product', 'search_read', [
                [['product_tmpl_id', '=', $templateId]],
            ], [
                'fields' => ['id'],
                'limit' => 1,
            ]);

            if (!empty($products) && isset($products[0]['id'])) {
                return (int) $products[0]['id'];
            }

            throw new \Exception('Failed to get product variant after creating template');
        } catch (\Exception $e) {
            Log::error('OdooClient: Error creating service product', [
                'product_name' => $product->name,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
