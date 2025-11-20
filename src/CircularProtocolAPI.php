<?php

namespace Circular\Protocol;

use Exception;
use phpseclib3\Crypt\EC;
use phpseclib3\Math\BigInteger;

/**
 * Circular Protocol PHP SDK
 * Version: 1.0.9
 *
 * Provides access to all Circular Protocol blockchain API endpoints.
 *
 * Example usage:
 * <code>
 * $api = new CircularProtocolAPI();
 * $response = $api->checkWallet([
 *     'Address' => '0x...',
 *     'Blockchain' => 'MainNet',
 *     'Version' => '1.0.9'
 * ]);
 * </code>
 */
class CircularProtocolAPI
{
    /** @var string NAG endpoint URL */
    private string $nagUrl;

    /** @var string NAG API key */
    private string $nagKey;

    /** @var array<string, string> HTTP headers */
    private array $headers;

    /** @var string Last error message */
    private string $lastError = '';

    /** @var bool Enable automatic preprocessing of parameters */
    private bool $autoPreprocess = true;

    /** @var string Default API version */
    private string $defaultVersion = '1.0.9';

    /** @var string|null Primary node address for blockchain queries */
    private ?string $nodeAddress = null;

    /**
     * Create a new Circular Protocol API client
     *
     * @param string|null $nagUrl Optional NAG endpoint URL
     * @param string|null $nagKey Optional NAG API key
     */
    public function __construct(?string $nagUrl = null, ?string $nagKey = null)
    {
        $this->nagUrl = $nagUrl ?? 'https://nag.circularlabs.io/NAG.php';
        $this->nagKey = $nagKey ?? '';
        $this->headers = [];
    }

    /**
     * Set custom NAG endpoint URL
     *
     * @param string $url NAG endpoint URL
     * @return void
     */
    public function setNagUrl(string $url): void
    {
        $this->nagUrl = $url;
    }

    /**
     * Get current NAG endpoint URL
     *
     * @return string Current NAG URL
     */
    public function getNagUrl(): string
    {
        return $this->nagUrl;
    }

    /**
     * Set NAG API key for authenticated requests
     *
     * @param string $key API key
     * @return void
     */
    public function setNagKey(string $key): void
    {
        $this->nagKey = $key;
    }

    /**
     * Get current NAG API key
     *
     * @return string Current NAG key
     */
    public function getNagKey(): string
    {
        return $this->nagKey;
    }

    /**
     * Enable or disable automatic preprocessing
     *
     * @param bool $enabled Enable automatic preprocessing
     * @return void
     */
    public function setAutoPreprocess(bool $enabled): void
    {
        $this->autoPreprocess = $enabled;
    }

    /**
     * Check if auto-preprocessing is enabled
     *
     * @return bool True if enabled
     */
    public function getAutoPreprocess(): bool
    {
        return $this->autoPreprocess;
    }

    /**
     * Set default API version
     *
     * @param string $version Default version string
     * @return void
     */
    public function setDefaultVersion(string $version): void
    {
        $this->defaultVersion = $version;
    }

    /**
     * Get default API version
     *
     * @return string Default version
     */
    public function getDefaultVersion(): string
    {
        return $this->defaultVersion;
    }

    /**
     * Set custom HTTP header
     *
     * @param string $key Header key
     * @param string $value Header value
     * @return void
     */
    public function setHeader(string $key, string $value): void
    {
        $this->headers[$key] = $value;
    }

    /**
     * Get SDK version
     *
     * @return string SDK version
     */
    public function getVersion(): string
    {
        return '1.0.9';
    }

    /**
     * Set primary node address for blockchain queries
     *
     * @param string $address Node address
     * @return void
     */
    public function setNode(string $address): void
    {
        $this->nodeAddress = $address;
    }

    /**
     * Get primary node address
     *
     * @return string|null Current node address
     */
    public function getNode(): ?string
    {
        return $this->nodeAddress;
    }

    /**
     * Clean up resources (HTTP client, etc.)
     *
     * @return void
     */
    public function dispose(): void
    {
        // Clear sensitive data
        $this->nagKey = '';
        $this->headers = [];
        $this->lastError = '';
        $this->nodeAddress = null;
    }

    /**
     * Preprocess request parameters
     * - Auto-strips 0x prefix from hex strings
     * - Auto-injects Version if not present
     * - Validates required fields
     *
     * @param array<string, mixed> $data Request data
     * @return array<string, mixed> Preprocessed data
     */
    private function preprocessRequest(array $data): array
    {
        if (!$this->autoPreprocess) {
            return $data;
        }

        // Auto-inject version if not present
        if (!isset($data['Version']) && !empty($this->defaultVersion)) {
            $data['Version'] = $this->defaultVersion;
        }

        // Auto-strip 0x prefix from common hex fields
        $hexFields = [
            'Address', 'From', 'To', 'ID', 'TransactionID',
            'Signature', 'PublicKey', 'Payload', 'Blockchain',
            'VoucherID', 'ContractAddress', 'Asset', 'Code'
        ];

        foreach ($hexFields as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = $this->hexFix($data[$field]);
            }
        }

        return $data;
    }

    /**
     * Make HTTP request to NAG endpoint
     *
     * @param string $endpoint Endpoint path (e.g., '/checkWallet')
     * @param array<string, mixed> $data Request payload
     * @return array<string, mixed> Full API response with Result and Response fields
     * @throws CircularProtocolException
     */
    private function makeRequest(string $endpoint, array $data): array
    {
        // Preprocess request data
        $data = $this->preprocessRequest($data);

        $url = $this->nagUrl . '?cep=Circular_' . $endpoint;

        // Build headers
        $headers = array_merge($this->headers, [
            'Content-Type' => 'application/json',
        ]);

        // Add NAG key if set
        if (!empty($this->nagKey)) {
            $headers['X-NAG-Key'] = $this->nagKey;
        }

        // Format headers for curl
        $curlHeaders = [];
        foreach ($headers as $key => $value) {
            $curlHeaders[] = "$key: $value";
        }

        // Initialize curl
        $ch = curl_init($url);
        if ($ch === false) {
            throw new CircularProtocolException('Failed to initialize curl', 0, $endpoint);
        }

        // Set curl options
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $curlHeaders,
            CURLOPT_TIMEOUT => 30,
        ]);

        // Execute request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        // Check for curl errors
        if ($response === false) {
            throw new CircularProtocolException(
                "Request failed: $error",
                0,
                $endpoint
            );
        }

        // Check HTTP status
        if ($httpCode !== 200) {
            throw new CircularProtocolException(
                "API error: HTTP $httpCode",
                $httpCode,
                $endpoint
            );
        }

        // Parse JSON response
        $responseString = is_string($response) ? $response : (string)$response;
        $result = json_decode($responseString, true);
        if ($result === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new CircularProtocolException(
                'Failed to parse JSON response: ' . json_last_error_msg(),
                0,
                $endpoint
            );
        }

        // Check API-level errors
        if (!isset($result['Result']) || $result['Result'] !== 200) {
            $errorMsg = $result['Response'] ?? 'API request failed';
            $resultCode = $result['Result'] ?? 0;
            throw new CircularProtocolException($errorMsg, $resultCode, $endpoint);
        }

        // Return full response (with Result and Response fields)
        return $result;
    }

    // ============================================================================
    // API Methods
    // ============================================================================
/**
 * Check if wallet exists
 * Checks whether a wallet address exists on the specified blockchain.
Returns existence status and confirms the address format.
 *
 * @param array<string, mixed> $request Request parameters
 * @return array<string, mixed> Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function checkWallet(array $request): array
{
    return $this->makeRequest('CheckWallet', $request);
}
/**
 * Get wallet information
 * Retrieves complete wallet information including balance and nonce.
Returns all wallet properties including current state on the blockchain.
 *
 * @param array<string, mixed> $request Request parameters
 * @return array<string, mixed> Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getWallet(array $request): array
{
    return $this->makeRequest('GetWallet', $request);
}
/**
 * Get latest transactions for wallet
 * Retrieves the latest transactions for a wallet address.
Returns an array of transaction objects with details.
 *
 * @param array<string, mixed> $request Request parameters
 * @return array<string, mixed> Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getLatestTransactions(array $request): array
{
    return $this->makeRequest('GetLatestTransactions', $request);
}
/**
 * Get wallet balance for specific asset
 * Retrieves the balance of a specified asset in a wallet.
Returns the balance amount for the requested asset.
 *
 * @param array<string, mixed> $request Request parameters
 * @return array<string, mixed> Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getWalletBalance(array $request): array
{
    return $this->makeRequest('GetWalletBalance', $request);
}
/**
 * Get wallet nonce
 * Retrieves the nonce (transaction counter) of a wallet.
The nonce is used for transaction ordering and must increment with each transaction.
 *
 * @param array<string, mixed> $request Request parameters
 * @return array<string, mixed> Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getWalletNonce(array $request): array
{
    return $this->makeRequest('GetWalletNonce', $request);
}
/**
 * Submit transaction to blockchain
 * Submits a transaction to the blockchain. Requires a complete signed transaction
including ID, addresses, payload, nonce, and signature.
 *
 * @param array<string, mixed> $request Request parameters
 * @return array<string, mixed> Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function sendTransaction(array $request): array
{
    return $this->makeRequest('AddTransaction', $request);
}
/**
 * Get pending transaction by ID
 * Searches for a transaction by ID among pending transactions.
Returns the transaction if it exists and is still pending.
 *
 * @param array<string, mixed> $request Request parameters
 * @return array<string, mixed> Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getPendingTransaction(array $request): array
{
    return $this->makeRequest('GetPendingTransaction', $request);
}
/**
 * Find transaction by ID
 * Finds a transaction by ID within a specified block range.
Searches through blocks to locate the transaction.
 *
 * @param array<string, mixed> $request Request parameters
 * @return array<string, mixed> Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getTransactionById(array $request): array
{
    return $this->makeRequest('GetTransactionbyID', $request);
}
/**
 * Find transactions by node ID
 * Finds transactions by node ID within a specified block range.
Returns all transactions associated with the node.
 *
 * @param array<string, mixed> $request Request parameters
 * @return array<string, mixed> Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getTransactionByNode(array $request): array
{
    return $this->makeRequest('GetTransactionbyNode', $request);
}
/**
 * Find transactions by address
 * Finds transactions by wallet address within a specified block range.
Returns transactions where the address is sender or recipient.
 *
 * @param array<string, mixed> $request Request parameters
 * @return array<string, mixed> Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getTransactionByAddress(array $request): array
{
    return $this->makeRequest('GetTransactionbyAddress', $request);
}
/**
 * Find transactions by date range
 * Finds transactions by wallet address within a specified date range.
Returns all transactions for the address between the dates.
 *
 * @param array<string, mixed> $request Request parameters
 * @return array<string, mixed> Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getTransactionByDate(array $request): array
{
    return $this->makeRequest('GetTransactionbyDate', $request);
}
/**
 * Get specific block
 * Retrieves a desired block by block number.
Returns complete block information including transactions and hash.
 *
 * @param array<string, mixed> $request Request parameters
 * @return array<string, mixed> Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getBlock(array $request): array
{
    return $this->makeRequest('GetBlock', $request);
}
/**
 * Get range of blocks
 * Retrieves all blocks in a specified range.
If End = 0, then Start is the number of blocks from the last one minted going backward.
 *
 * @param array<string, mixed> $request Request parameters
 * @return array<string, mixed> Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getBlockRange(array $request): array
{
    return $this->makeRequest('GetBlockRange', $request);
}
/**
 * Get blockchain height
 * Retrieves the blockchain block height (total number of blocks).
 *
 * @param array<string, mixed> $request Request parameters
 * @return array<string, mixed> Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getBlockCount(array $request): array
{
    return $this->makeRequest('GetBlockHeight', $request);
}
/**
 * Get blockchain analytics
 * Retrieves blockchain analytics and statistics.
Returns comprehensive information about the blockchain state.
 *
 * @param array<string, mixed> $request Request parameters
 * @return array<string, mixed> Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getAnalytics(array $request): array
{
    return $this->makeRequest('GetAnalytics', $request);
}
/**
 * Test smart contract execution
 * Tests smart contract execution locally without sending a transaction.
Useful for testing contract logic before deploying or executing.
 *
 * @param array<string, mixed> $request Request parameters
 * @return array<string, mixed> Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function testContract(array $request): array
{
    return $this->makeRequest('TestContract', $request);
}
/**
 * Call smart contract function
 * Calls a smart contract function on the blockchain.
Executes the specified function with provided parameters.
 *
 * @param array<string, mixed> $request Request parameters
 * @return array<string, mixed> Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function callContract(array $request): array
{
    return $this->makeRequest('CallContract', $request);
}
/**
 * List all assets on blockchain
 * Retrieves the list of all assets minted on a specific blockchain.
Returns an array of asset information.
 *
 * @param array<string, mixed> $request Request parameters
 * @return array<string, mixed> Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getAssetList(array $request): array
{
    return $this->makeRequest('GetAssetList', $request);
}
/**
 * Get specific asset information
 * Retrieves an asset descriptor with complete asset information.
Returns detailed information about the specified asset.
 *
 * @param array<string, mixed> $request Request parameters
 * @return array<string, mixed> Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getAsset(array $request): array
{
    return $this->makeRequest('GetAsset', $request);
}
/**
 * Get asset supply information
 * Retrieves the total, circulating, and residual supply of a specified asset.
Returns comprehensive supply metrics.
 *
 * @param array<string, mixed> $request Request parameters
 * @return array<string, mixed> Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getAssetSupply(array $request): array
{
    return $this->makeRequest('GetAssetSupply', $request);
}
/**
 * Retrieve voucher information
 * Retrieves an existing voucher by code.
Code is automatically stripped of 0x prefix if present.
 *
 * @param array<string, mixed> $request Request parameters
 * @return array<string, mixed> Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getVoucher(array $request): array
{
    return $this->makeRequest('GetVoucher', $request);
}
/**
 * Resolve domain to wallet address
 * Resolves a domain name to a wallet address.
A single wallet can have multiple domain associations.
 *
 * @param array<string, mixed> $request Request parameters
 * @return array<string, mixed> Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getDomain(array $request): array
{
    return $this->makeRequest('ResolveDomain', $request);
}
/**
 * List available blockchains
 * Retrieves the list of blockchains available in the network.
Returns information about all active and inactive blockchains.
 *
 * @param array<string, mixed> $request Request parameters
 * @return array<string, mixed> Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getBlockchains(array $request): array
{
    return $this->makeRequest('GetBlockchains', $request);
}

    // ============================================================================
    // Convenience Methods
    // ============================================================================
    // These methods wrap underlying API calls to simplify common workflows

/**
 * Register wallet on blockchain
 * Registers a wallet on the specified blockchain. Accepts either a full request
 * array or will use sendTransaction directly.
 *
 * Without registration, the wallet will not be reachable on the blockchain.
 * The same wallet can be registered on multiple blockchains.
 *
 * Expected request format:
 * {
 *   "Blockchain": "0x...",
 *   "AccountName": "myaccount",
 *   "PublicKey": "128-character-hex-pubkey",
 *   "Signature": "signature-of-blockchain+accountname+publickey",
 *   "Version": "1.0.9"
 * }
 *
 * @param array<string, mixed> $request Request parameters for wallet registration
 * @return array<string, mixed> API response with Result and Response fields
 * @throws CircularProtocolException
 */
public function registerWallet(array $request): array
{
    return $this->makeRequest('RegisterWallet', $request);
}

    // ============================================================================
    // Helper Methods - Cryptography
    // ============================================================================

/**
 * Sign a message using secp256k1 with DER encoding
 *
 * @param string $message Message to sign (will be SHA256 hashed)
 * @param string $privateKey Private key in hex format (with or without '0x' prefix)
 * @return string DER-encoded signature as hex string
 */
public function signMessage(string $message, string $privateKey): string {
    // Remove 0x prefix if present
    $cleanKey = $this->hexFix($privateKey);

    // Create private key
    $privateKeyObj = EC::loadPrivateKey([
        'curve' => 'secp256k1',
        'secret' => new BigInteger($cleanKey, 16)
    ]);

    // Hash the message
    $messageHash = hash('sha256', $message, true);

    // Sign with DER encoding (default format in phpseclib)
    $signature = $privateKeyObj->sign($messageHash);

    // Return hex-encoded signature
    return bin2hex($signature);
}

/**
 * Verify a DER-encoded signature
 *
 * @param string $publicKey Public key in hex format (uncompressed, 64 bytes without 0x04 prefix)
 * @param string $message Original message that was signed
 * @param string $signatureHex DER-encoded signature in hex format
 * @return bool true if signature is valid, false otherwise
 */
public function verifySignature(string $publicKey, string $message, string $signatureHex): bool {
    try {
        // Parse public key
        $cleanPubKey = $this->hexFix($publicKey);
        $pubKeyBytes = hex2bin($cleanPubKey);
        if ($pubKeyBytes === false) {
            return false;
        }

        // Add uncompressed point prefix if needed (0x04)
        if (strlen($pubKeyBytes) === 64) {
            $pubKeyBytes = "\x04" . $pubKeyBytes;
        }

        // Extract X and Y coordinates
        $x = substr($pubKeyBytes, 1, 32);
        $y = substr($pubKeyBytes, 33, 32);

        // Create public key
        $publicKeyObj = EC::loadPublicKey([
            'curve' => 'secp256k1',
            'QA' => [
                'x' => new BigInteger(bin2hex($x), 16),
                'y' => new BigInteger(bin2hex($y), 16)
            ]
        ]);

        // Hash the message
        $messageHash = hash('sha256', $message, true);

        // Verify DER-encoded signature
        $signatureBytes = hex2bin($this->hexFix($signatureHex));

        return $publicKeyObj->verify($messageHash, $signatureBytes);
    } catch (\Exception $e) {
        return false;
    }
}

/**
 * Derive public key from private key
 *
 * @param string $privateKey Private key in hex format (with or without '0x' prefix)
 * @return string Public key in uncompressed hex format (64 bytes, without 0x04 prefix)
 */
public function getPublicKey(string $privateKey): string {
    // Remove 0x prefix if present
    $cleanKey = $this->hexFix($privateKey);

    // Create private key
    $privateKeyObj = EC::loadPrivateKey([
        'curve' => 'secp256k1',
        'secret' => new BigInteger($cleanKey, 16)
    ]);

    // Get public key
    $publicKeyObj = $privateKeyObj->getPublicKey();
    $point = $publicKeyObj->getEncodedCoordinates();

    // Extract X and Y coordinates (remove 0x04 prefix)
    $publicKeyBytes = hex2bin($point);
    if ($publicKeyBytes === false) {
        throw new \RuntimeException('Failed to decode public key');
    }
    if (isset($publicKeyBytes[0]) && $publicKeyBytes[0] === "\x04") {
        $publicKeyBytes = substr($publicKeyBytes, 1);
    }

    return bin2hex($publicKeyBytes);
}

/**
 * Compute SHA256 hash of a string
 *
 * @param string $str String to hash
 * @return string SHA256 hash as hex string
 */
public function hashString(string $str): string {
    return hash('sha256', $str);
}

    // ============================================================================
    // Helper Methods - Encoding
    // ============================================================================

/**
 * Normalize hex strings (remove 0x prefix if present)
 *
 * @param string $hexString Hex string with or without 0x prefix
 * @return string Normalized hex string without 0x prefix
 */
public function hexFix(string $hexString): string {
    if (str_starts_with($hexString, '0x') || str_starts_with($hexString, '0X')) {
        return substr($hexString, 2);
    }
    return $hexString;
}

/**
 * Convert string to hex encoding
 *
 * @param string $str String to convert
 * @return string Hex-encoded string
 */
public function stringToHex(string $str): string {
    return bin2hex($str);
}

/**
 * Convert hex encoding to string
 *
 * @param string $hexString Hex-encoded string
 * @return string Decoded string
 */
public function hexToString(string $hexString): string {
    $normalized = $this->hexFix($hexString);
    $result = hex2bin($normalized);
    if ($result === false) {
        throw new \RuntimeException('Failed to decode hex string');
    }
    return $result;
}

/**
 * Pad number with leading zero if single digit
 *
 * @param int $num Number to pad
 * @return string Padded string
 */
private function padNumber(int $num): string {
    return str_pad((string)$num, 2, '0', STR_PAD_LEFT);
}

/**
 * Get current timestamp in Circular Protocol format
 * Format: YYYY:MM:DD-hh:mm:ss (UTC)
 *
 * @return string Formatted timestamp string
 */
public function getFormattedTimestamp(): string {
    $now = new \DateTime('now', new \DateTimeZone('UTC'));
    return sprintf('%d:%s:%s-%s:%s:%s',
        $now->format('Y'),
        $this->padNumber((int)$now->format('m')),
        $this->padNumber((int)$now->format('d')),
        $this->padNumber((int)$now->format('H')),
        $this->padNumber((int)$now->format('i')),
        $this->padNumber((int)$now->format('s')));
}

    // ============================================================================
    // Helper Methods - Advanced
    // ============================================================================

/**
 * Get last error message
 *
 * @return string Last error message
 */
public function getError(): string {
    return $this->lastError;
}

/**
 * Handle error and store error message
 *
 * @param \Throwable|string $error Error object or string
 * @return void
 */
private function handleError(\Throwable|string $error): void {
    if ($error instanceof \Throwable) {
        $this->lastError = $error->getMessage();
    } elseif (is_string($error)) {
        $this->lastError = $error;
    } else {
        $this->lastError = 'Unknown error';
    }
}

/**
 * Poll for transaction confirmation
 *
 * @param string $blockchain Blockchain network (e.g., 'MainNet', 'testnet')
 * @param string $txID Transaction ID to monitor
 * @param string $start Start block number for search
 * @param string $end End block number for search
 * @param int $timeoutSec Maximum time to wait in seconds (default: 120)
 * @param int $intervalSec Polling interval in seconds (default: 5)
 * @return array<string, mixed> Transaction response when confirmed
 * @throws CircularProtocolException if transaction fails or times out
 */
public function getTransactionOutcome(
    string $blockchain,
    string $txID,
    string $start,
    string $end,
    int $timeoutSec = 120,
    int $intervalSec = 5
): array {
    $startTime = time();

    while (true) {
        // Check if timeout exceeded
        $elapsed = time() - $startTime;
        if ($elapsed >= $timeoutSec) {
            $error = "Transaction {$txID} timed out after {$timeoutSec} seconds";
            $this->handleError($error);
            throw new CircularProtocolException($error, 0, 'getTransactionOutcome');
        }

        try {
            // Check transaction status
            $tx = $this->getTransactionById([
                'Blockchain' => $blockchain,
                'ID' => $txID,
                'Start' => $start,
                'End' => $end,
                'Version' => '2.0.0-alpha.1',
            ]);

            // Check if transaction is confirmed (has BlockNumber)
            if (isset($tx['Response']['BlockNumber']) && $tx['Response']['BlockNumber'] > 0) {
                // Transaction confirmed
                return $tx;
            }

            // Still pending, wait before next check
            sleep($intervalSec);

        } catch (CircularProtocolException $error) {
            // If error is not just "pending", rethrow
            if (stripos($error->getMessage(), 'pending') === false) {
                $this->handleError($error);
                throw $error;
            }

            // Otherwise, wait and retry
            sleep($intervalSec);
        }
    }
}
}

/**
 * Exception thrown when API requests fail
 */
class CircularProtocolException extends Exception
{
    /** @var int HTTP status code or API result code */
    private int $statusCode;

    /** @var string Endpoint that failed */
    private string $endpoint;

    /**
     * @param string $message Error message
     * @param int $statusCode HTTP or API status code
     * @param string $endpoint Endpoint path
     */
    public function __construct(string $message, int $statusCode, string $endpoint)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->endpoint = $endpoint;
    }

    /**
     * Get status code
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get endpoint
     *
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }
}