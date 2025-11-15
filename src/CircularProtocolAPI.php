<?php

namespace Circular\Protocol;

use Exception;

/**
 * Circular Protocol PHP SDK
 * Generated from Nickel API specification
 * Version: 1.0.8
 *
 * Provides access to all Circular Protocol blockchain API endpoints.
 *
 * Example usage:
 * <code>
 * $api = new CircularProtocolAPI();
 * $response = $api->checkWallet([
 *     'Address' => '0x...',
 *     'Blockchain' => 'MainNet',
 *     'Version' => '1.0.8'
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

    /**
     * Create a new Circular Protocol API client
     *
     * @param string|null $nagUrl Optional NAG endpoint URL
     * @param string|null $nagKey Optional NAG API key
     */
    public function __construct(?string $nagUrl = null, ?string $nagKey = null)
    {
        $this->nagUrl = $nagUrl ?? 'https://nag.circularlabs.io/NAG.php?cep=';
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
     * Make HTTP request to NAG endpoint
     *
     * @param string $endpoint Endpoint path (e.g., '/checkWallet')
     * @param array $data Request payload
     * @return array Full API response with Result and Response fields
     * @throws CircularProtocolException
     */
    private function makeRequest(string $endpoint, array $data): array
    {
        $url = $this->nagUrl . 'Circular_' . $endpoint . '_';

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
        $result = json_decode($response, true);
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
 * @param array $request Request parameters
 * @return array Response with Result and Response fields
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
 * @param array $request Request parameters
 * @return array Response with Result and Response fields
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
 * @param array $request Request parameters
 * @return array Response with Result and Response fields
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
 * @param array $request Request parameters
 * @return array Response with Result and Response fields
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
 * @param array $request Request parameters
 * @return array Response with Result and Response fields
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
 * @param array $request Request parameters
 * @return array Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function addTransaction(array $request): array
{
    return $this->makeRequest('AddTransaction', $request);
}
/**
 * Get pending transaction by ID
 * Searches for a transaction by ID among pending transactions.
Returns the transaction if it exists and is still pending.
 *
 * @param array $request Request parameters
 * @return array Response with Result and Response fields
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
 * @param array $request Request parameters
 * @return array Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getTransactionbyID(array $request): array
{
    return $this->makeRequest('GetTransactionbyID', $request);
}
/**
 * Find transactions by node ID
 * Finds transactions by node ID within a specified block range.
Returns all transactions associated with the node.
 *
 * @param array $request Request parameters
 * @return array Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getTransactionbyNode(array $request): array
{
    return $this->makeRequest('GetTransactionbyNode', $request);
}
/**
 * Find transactions by address
 * Finds transactions by wallet address within a specified block range.
Returns transactions where the address is sender or recipient.
 *
 * @param array $request Request parameters
 * @return array Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getTransactionbyAddress(array $request): array
{
    return $this->makeRequest('GetTransactionbyAddress', $request);
}
/**
 * Find transactions by date range
 * Finds transactions by wallet address within a specified date range.
Returns all transactions for the address between the dates.
 *
 * @param array $request Request parameters
 * @return array Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getTransactionbyDate(array $request): array
{
    return $this->makeRequest('GetTransactionbyDate', $request);
}
/**
 * Get specific block
 * Retrieves a desired block by block number.
Returns complete block information including transactions and hash.
 *
 * @param array $request Request parameters
 * @return array Response with Result and Response fields
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
 * @param array $request Request parameters
 * @return array Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getBlockRange(array $request): array
{
    return $this->makeRequest('GetBlockRange', $request);
}
/**
 * Get blockchain height
 * Retrieves the blockchain block height (total number of blocks).
Also known as getBlockHeight in some documentation.
 *
 * @param array $request Request parameters
 * @return array Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getBlockCount(array $request): array
{
    return $this->makeRequest('GetBlockCount', $request);
}
/**
 * Get blockchain analytics
 * Retrieves blockchain analytics and statistics.
Returns comprehensive information about the blockchain state.
 *
 * @param array $request Request parameters
 * @return array Response with Result and Response fields
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
 * @param array $request Request parameters
 * @return array Response with Result and Response fields
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
 * @param array $request Request parameters
 * @return array Response with Result and Response fields
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
 * @param array $request Request parameters
 * @return array Response with Result and Response fields
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
 * @param array $request Request parameters
 * @return array Response with Result and Response fields
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
 * @param array $request Request parameters
 * @return array Response with Result and Response fields
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
 * @param array $request Request parameters
 * @return array Response with Result and Response fields
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
Also known as resolveDomain.
 *
 * @param array $request Request parameters
 * @return array Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getDomain(array $request): array
{
    return $this->makeRequest('GetDomain', $request);
}
/**
 * List available blockchains
 * Retrieves the list of blockchains available in the network.
Returns information about all active and inactive blockchains.
 *
 * @param array $request Request parameters
 * @return array Response with Result and Response fields
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
 * Register wallet on blockchain (Convenience Method)
 * Registers a wallet on the specified blockchain by creating and sending
a C_TYPE_REGISTERWALLET transaction. This convenience method handles all
transaction construction internally:

- Derives From/To addresses from public key (sha256)
- Builds Payload: hex(JSON.stringify({Action: "CP_REGISTERWALLET", PublicKey: publicKey}))
- Calculates transaction ID: sha256(blockchain + from + to + payload + nonce + timestamp)
- Sets Nonce to "0" and Signature to "" (empty for registration)
- Calls sendTransaction with constructed parameters

Without registration, the wallet will not be reachable on the blockchain.
The same wallet can be registered on multiple blockchains.
 *
 * This is a convenience method that wraps sendTransaction().
 * It handles transaction construction internally.
 *
 * @param string $blockchain Blockchain where the wallet will be registered
 * @param string $publicKey Wallet public key (128 hex characters)
 * @return array Same as send_transaction response
 * @throws CircularProtocolException
 */
public function register_wallet(string $blockchain, string $publicKey): array
{
    // Derive addresses from public key
    $from = $this->hashString($publicKey);
    $to = $from;
    $nonce = '0';
    $type = 'C_TYPE_REGISTERWALLET';

    // Build payload
    $payloadObj = [
        'Action' => 'CP_REGISTERWALLET',
        'PublicKey' => $publicKey
    ];
    $payload = $this->stringToHex(json_encode($payloadObj, JSON_UNESCAPED_SLASHES));
    $timestamp = $this->getFormattedTimestamp();

    // Calculate transaction ID
    $id = $this->hashString($blockchain . $from . $to . $payload . $nonce . $timestamp);
    $signature = '';

    // Build request
    $request = [
        'ID' => $id,
        'From' => $from,
        'To' => $to,
        'Timestamp' => $timestamp,
        'Type' => $type,
        'Payload' => $payload,
        'Nonce' => $nonce,
        'Signature' => $signature,
        'Blockchain' => $blockchain,
        'Version' => '1.0.8'
    ];

    // Call sendTransaction
    return $this->send_transaction($request);
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
    if ($publicKeyBytes[0] === "\x04") {
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
private function hexFix(string $hexString): string {
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
    return hex2bin($normalized);
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
 * @return array Transaction response when confirmed
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
            $tx = $this->getTransactionbyID([
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