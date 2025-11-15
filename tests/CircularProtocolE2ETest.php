<?php

namespace Circular\Protocol\Tests;

use Circular\Protocol\CircularProtocolAPI;
use Circular\Protocol\CircularProtocolException;
use PHPUnit\Framework\TestCase;

/**
 * Circular Protocol PHP SDK E2E Tests
 * Generated from Nickel E2E test specifications
 *
 * These tests run against REAL NAG endpoints.
 * They only execute when required environment variables are present.
 *
 * Required ENV vars (read operations):
 * - CIRCULAR_TEST_ADDRESS: Test wallet address (must exist on blockchain)
 *
 * Required ENV vars (write operations):
 * - CIRCULAR_PRIVATE_KEY: Private key for signing transactions (32-byte hex)
 *
 * Optional ENV vars:
 * - CIRCULAR_NAG_URL: NAG endpoint URL (default: https://nag.circularlabs.io/NAG.php?cep=)
 * - CIRCULAR_TEST_BLOCKCHAIN: Blockchain network (default: 0x8a20baa40c45dc5055aeb26197c203e576ef389d9acb171bd62da11dc5ad72b2)
 * - CIRCULAR_API_KEY: Optional API key
 * - CIRCULAR_E2E_TIMEOUT: Request timeout in ms (default: 30000)
 *
 * Run read-only tests with:
 *   CIRCULAR_TEST_ADDRESS=0x... composer test:e2e
 *
 * Run write operation tests with:
 *   CIRCULAR_PRIVATE_KEY=... composer test:e2e
 *   ⚠️  WARNING: This will create real transactions on the blockchain!
 *
 * Or skip if ENV vars not present:
 *   composer test:e2e  # Will skip all tests
 */
class CircularProtocolE2ETest extends TestCase
{
    private CircularProtocolAPI $api;
    private static array $readEnvVars = ['CIRCULAR_TEST_ADDRESS'];
    private static array $writeEnvVars = ['CIRCULAR_PRIVATE_KEY'];
    private static bool $hasReadEnv;
    private static bool $hasWriteEnv;

    public static function setUpBeforeClass(): void
    {
        // Check for read-only test environment variables
        $missingReadVars = array_filter(self::$readEnvVars, fn($v) => !getenv($v));

        // Check for write test environment variables
        $missingWriteVars = array_filter(self::$writeEnvVars, fn($v) => !getenv($v));

        self::$hasReadEnv = empty($missingReadVars);
        self::$hasWriteEnv = empty($missingWriteVars);

        if (!self::$hasReadEnv && !self::$hasWriteEnv) {
            echo "⏭️  Skipping all E2E tests - missing required environment variables\n";
            echo "\nFor read-only tests:\n";
            echo "  CIRCULAR_TEST_ADDRESS=0x... composer test:e2e\n";
            echo "\nFor write operation tests:\n";
            echo "  CIRCULAR_PRIVATE_KEY=... composer test:e2e\n";
            echo "  ⚠️  WARNING: Write tests create real blockchain transactions!\n";
            self::markTestSkipped('Missing required environment variables');
        }

        $nagUrl = getenv('CIRCULAR_NAG_URL') ?: 'https://nag.circularlabs.io/NAG.php?cep=';
        $blockchain = getenv('CIRCULAR_TEST_BLOCKCHAIN') ?: '0x8a20baa40c45dc5055aeb26197c203e576ef389d9acb171bd62da11dc5ad72b2';

        echo "\n🌐 Running E2E tests against: $nagUrl\n";
        if (self::$hasReadEnv) {
            echo "📍 Test address: " . getenv('CIRCULAR_TEST_ADDRESS') . "\n";
        }
        if (self::$hasWriteEnv) {
            echo "🔑 Private key: ***REDACTED***\n";
        }
        echo "⛓️  Blockchain: $blockchain\n\n";

        if (self::$hasWriteEnv) {
            fwrite(STDERR, "⚠️  WARNING: Write operation tests will create REAL transactions on the blockchain!\n");
            fwrite(STDERR, "⚠️  Ensure you are using a test blockchain and test funds.\n");
        }
    }

    protected function setUp(): void
    {
        $nagUrl = getenv('CIRCULAR_NAG_URL') ?: 'https://nag.circularlabs.io/NAG.php?cep=';
        $apiKey = getenv('CIRCULAR_API_KEY');
        $this->api = new CircularProtocolAPI($nagUrl, $apiKey);
    }

    // Wallet API E2E Tests (Read-Only)
    public function testCheck_wallet(): void
    {
        $requestJson = '{
  "Address": "${CIRCULAR_TEST_ADDRESS}",
  "Blockchain": "${CIRCULAR_TEST_BLOCKCHAIN}",
  "Version": "1.0.8"
}';
        $requestJson = str_replace('${CIRCULAR_TEST_ADDRESS}', getenv('CIRCULAR_TEST_ADDRESS') ?: '', $requestJson);
        $requestJson = str_replace('${CIRCULAR_TEST_BLOCKCHAIN}', getenv('CIRCULAR_TEST_BLOCKCHAIN') ?: 'MainNet', $requestJson);
        $request = json_decode($requestJson, true);

        $result = $this->api->checkWallet($request);

$this->assertNotNull($result['Result'])

        echo "  ✅ E2E: Check if test wallet exists on blockchain\n";
    }
    public function testGet_latest_transactions(): void
    {
        $requestJson = '{
  "Address": "${CIRCULAR_TEST_ADDRESS}",
  "Blockchain": "${CIRCULAR_TEST_BLOCKCHAIN}",
  "Version": "1.0.8"
}';
        $requestJson = str_replace('${CIRCULAR_TEST_ADDRESS}', getenv('CIRCULAR_TEST_ADDRESS') ?: '', $requestJson);
        $requestJson = str_replace('${CIRCULAR_TEST_BLOCKCHAIN}', getenv('CIRCULAR_TEST_BLOCKCHAIN') ?: 'MainNet', $requestJson);
        $request = json_decode($requestJson, true);

        $result = $this->api->getLatestTransactions($request);

$this->assertNotNull($result['Result'])

        echo "  ✅ E2E: Get latest transactions for wallet\n";
    }
    public function testGet_wallet(): void
    {
        $requestJson = '{
  "Address": "${CIRCULAR_TEST_ADDRESS}",
  "Blockchain": "${CIRCULAR_TEST_BLOCKCHAIN}",
  "Version": "1.0.8"
}';
        $requestJson = str_replace('${CIRCULAR_TEST_ADDRESS}', getenv('CIRCULAR_TEST_ADDRESS') ?: '', $requestJson);
        $requestJson = str_replace('${CIRCULAR_TEST_BLOCKCHAIN}', getenv('CIRCULAR_TEST_BLOCKCHAIN') ?: 'MainNet', $requestJson);
        $request = json_decode($requestJson, true);

        $result = $this->api->getWallet($request);

$this->assertNotNull($result['Result'])

        echo "  ✅ E2E: Retrieve wallet details from blockchain\n";
    }
    public function testGet_wallet_balance(): void
    {
        $requestJson = '{
  "Address": "${CIRCULAR_TEST_ADDRESS}",
  "Asset": "CIRX",
  "Blockchain": "${CIRCULAR_TEST_BLOCKCHAIN}",
  "Version": "1.0.8"
}';
        $requestJson = str_replace('${CIRCULAR_TEST_ADDRESS}', getenv('CIRCULAR_TEST_ADDRESS') ?: '', $requestJson);
        $requestJson = str_replace('${CIRCULAR_TEST_BLOCKCHAIN}', getenv('CIRCULAR_TEST_BLOCKCHAIN') ?: 'MainNet', $requestJson);
        $request = json_decode($requestJson, true);

        $result = $this->api->getWalletBalance($request);

$this->assertNotNull($result['Result'])

        echo "  ✅ E2E: Get wallet balance from blockchain\n";
    }
    public function testGet_wallet_nonce(): void
    {
        $requestJson = '{
  "Address": "${CIRCULAR_TEST_ADDRESS}",
  "Blockchain": "${CIRCULAR_TEST_BLOCKCHAIN}",
  "Version": "1.0.8"
}';
        $requestJson = str_replace('${CIRCULAR_TEST_ADDRESS}', getenv('CIRCULAR_TEST_ADDRESS') ?: '', $requestJson);
        $requestJson = str_replace('${CIRCULAR_TEST_BLOCKCHAIN}', getenv('CIRCULAR_TEST_BLOCKCHAIN') ?: 'MainNet', $requestJson);
        $request = json_decode($requestJson, true);

        $result = $this->api->getWalletNonce($request);

$this->assertNotNull($result['Result'])

        echo "  ✅ E2E: Get wallet nonce from blockchain\n";
    }

    // Transaction API E2E Tests (Read-Only)
    public function testGet_pending_transaction(): void
    {
        $requestJson = '{
  "Blockchain": "${CIRCULAR_TEST_BLOCKCHAIN}",
  "Version": "1.0.8"
}';
        $requestJson = str_replace('${CIRCULAR_TEST_ADDRESS}', getenv('CIRCULAR_TEST_ADDRESS') ?: '', $requestJson);
        $requestJson = str_replace('${CIRCULAR_TEST_BLOCKCHAIN}', getenv('CIRCULAR_TEST_BLOCKCHAIN') ?: 'MainNet', $requestJson);
        $request = json_decode($requestJson, true);

        $result = $this->api->getPendingTransaction($request);

$this->assertNotNull($result['Result'])

        echo "  ✅ E2E: Get pending transactions\n";
    }
    public function testGet_transaction_by_address(): void
    {
        $requestJson = '{
  "Address": "${CIRCULAR_TEST_ADDRESS}",
  "Blockchain": "${CIRCULAR_TEST_BLOCKCHAIN}",
  "Version": "1.0.8"
}';
        $requestJson = str_replace('${CIRCULAR_TEST_ADDRESS}', getenv('CIRCULAR_TEST_ADDRESS') ?: '', $requestJson);
        $requestJson = str_replace('${CIRCULAR_TEST_BLOCKCHAIN}', getenv('CIRCULAR_TEST_BLOCKCHAIN') ?: 'MainNet', $requestJson);
        $request = json_decode($requestJson, true);

        $result = $this->api->getTransactionbyAddress($request);

$this->assertNotNull($result['Result'])

        echo "  ✅ E2E: Get transactions by wallet address\n";
    }
    public function testGet_transaction_by_date(): void
    {
        $requestJson = '{
  "Blockchain": "${CIRCULAR_TEST_BLOCKCHAIN}",
  "EndDate": "2024-12-31",
  "StartDate": "2024-01-01",
  "Version": "1.0.8"
}';
        $requestJson = str_replace('${CIRCULAR_TEST_ADDRESS}', getenv('CIRCULAR_TEST_ADDRESS') ?: '', $requestJson);
        $requestJson = str_replace('${CIRCULAR_TEST_BLOCKCHAIN}', getenv('CIRCULAR_TEST_BLOCKCHAIN') ?: 'MainNet', $requestJson);
        $request = json_decode($requestJson, true);

        $result = $this->api->getTransactionbyDate($request);

$this->assertNotNull($result['Result'])

        echo "  ✅ E2E: Get transactions by date range\n";
    }
    public function testGet_transaction_by_id(): void
    {
        $requestJson = '{
  "Blockchain": "${CIRCULAR_TEST_BLOCKCHAIN}",
  "TransactionID": "0x0000000000000000000000000000000000000000000000000000000000000000",
  "Version": "1.0.8"
}';
        $requestJson = str_replace('${CIRCULAR_TEST_ADDRESS}', getenv('CIRCULAR_TEST_ADDRESS') ?: '', $requestJson);
        $requestJson = str_replace('${CIRCULAR_TEST_BLOCKCHAIN}', getenv('CIRCULAR_TEST_BLOCKCHAIN') ?: 'MainNet', $requestJson);
        $request = json_decode($requestJson, true);

        $result = $this->api->getTransactionbyID($request);

$this->assertNotNull($result['Result'])

        echo "  ✅ E2E: Get transaction by transaction ID\n";
    }
    public function testGet_transaction_by_node(): void
    {
        $requestJson = '{
  "Blockchain": "${CIRCULAR_TEST_BLOCKCHAIN}",
  "NodeID": "node-0001",
  "Version": "1.0.8"
}';
        $requestJson = str_replace('${CIRCULAR_TEST_ADDRESS}', getenv('CIRCULAR_TEST_ADDRESS') ?: '', $requestJson);
        $requestJson = str_replace('${CIRCULAR_TEST_BLOCKCHAIN}', getenv('CIRCULAR_TEST_BLOCKCHAIN') ?: 'MainNet', $requestJson);
        $request = json_decode($requestJson, true);

        $result = $this->api->getTransactionbyNode($request);

$this->assertNotNull($result['Result'])

        echo "  ✅ E2E: Get transactions by node ID\n";
    }

    // Asset API E2E Tests (Read-Only)
    public function testGet_asset(): void
    {
        $requestJson = '{
  "AssetName": "CIRX",
  "Blockchain": "${CIRCULAR_TEST_BLOCKCHAIN}",
  "Version": "1.0.8"
}';
        $requestJson = str_replace('${CIRCULAR_TEST_ADDRESS}', getenv('CIRCULAR_TEST_ADDRESS') ?: '', $requestJson);
        $requestJson = str_replace('${CIRCULAR_TEST_BLOCKCHAIN}', getenv('CIRCULAR_TEST_BLOCKCHAIN') ?: 'MainNet', $requestJson);
        $request = json_decode($requestJson, true);

        $result = $this->api->getAsset($request);

$this->assertNotNull($result['Result'])

        echo "  ✅ E2E: Get specific asset information\n";
    }
    public function testGet_asset_list(): void
    {
        $requestJson = '{
  "Blockchain": "${CIRCULAR_TEST_BLOCKCHAIN}",
  "Version": "1.0.8"
}';
        $requestJson = str_replace('${CIRCULAR_TEST_ADDRESS}', getenv('CIRCULAR_TEST_ADDRESS') ?: '', $requestJson);
        $requestJson = str_replace('${CIRCULAR_TEST_BLOCKCHAIN}', getenv('CIRCULAR_TEST_BLOCKCHAIN') ?: 'MainNet', $requestJson);
        $request = json_decode($requestJson, true);

        $result = $this->api->getAssetList($request);

$this->assertNotNull($result['Result'])

        echo "  ✅ E2E: Get list of all assets on blockchain\n";
    }
    public function testGet_asset_supply(): void
    {
        $requestJson = '{
  "AssetName": "CIRX",
  "Blockchain": "${CIRCULAR_TEST_BLOCKCHAIN}",
  "Version": "1.0.8"
}';
        $requestJson = str_replace('${CIRCULAR_TEST_ADDRESS}', getenv('CIRCULAR_TEST_ADDRESS') ?: '', $requestJson);
        $requestJson = str_replace('${CIRCULAR_TEST_BLOCKCHAIN}', getenv('CIRCULAR_TEST_BLOCKCHAIN') ?: 'MainNet', $requestJson);
        $request = json_decode($requestJson, true);

        $result = $this->api->getAssetSupply($request);

$this->assertNotNull($result['Result'])

        echo "  ✅ E2E: Get asset supply information\n";
    }
    public function testGet_voucher(): void
    {
        $requestJson = '{
  "Blockchain": "${CIRCULAR_TEST_BLOCKCHAIN}",
  "Version": "1.0.8",
  "VoucherID": "test-voucher-id"
}';
        $requestJson = str_replace('${CIRCULAR_TEST_ADDRESS}', getenv('CIRCULAR_TEST_ADDRESS') ?: '', $requestJson);
        $requestJson = str_replace('${CIRCULAR_TEST_BLOCKCHAIN}', getenv('CIRCULAR_TEST_BLOCKCHAIN') ?: 'MainNet', $requestJson);
        $request = json_decode($requestJson, true);

        $result = $this->api->getVoucher($request);

$this->assertNotNull($result['Result'])

        echo "  ✅ E2E: Get voucher details\n";
    }

    // Network API E2E Tests (Read-Only)
    public function testGet_blockchains(): void
    {
        $requestJson = '{
  "Version": "1.0.8"
}';
        $requestJson = str_replace('${CIRCULAR_TEST_ADDRESS}', getenv('CIRCULAR_TEST_ADDRESS') ?: '', $requestJson);
        $requestJson = str_replace('${CIRCULAR_TEST_BLOCKCHAIN}', getenv('CIRCULAR_TEST_BLOCKCHAIN') ?: 'MainNet', $requestJson);
        $request = json_decode($requestJson, true);

        $result = $this->api->getBlockchains($request);

$this->assertEquals(200, $result['Result'])
$this->assertIsArray($result['Response']['Blockchains'])

        echo "  ✅ E2E: Retrieve list of available blockchains\n";
    }

    // Block API E2E Tests (Read-Only)
    public function testGet_analytics(): void
    {
        $requestJson = '{
  "Blockchain": "${CIRCULAR_TEST_BLOCKCHAIN}",
  "Version": "1.0.8"
}';
        $requestJson = str_replace('${CIRCULAR_TEST_ADDRESS}', getenv('CIRCULAR_TEST_ADDRESS') ?: '', $requestJson);
        $requestJson = str_replace('${CIRCULAR_TEST_BLOCKCHAIN}', getenv('CIRCULAR_TEST_BLOCKCHAIN') ?: 'MainNet', $requestJson);
        $request = json_decode($requestJson, true);

        $result = $this->api->getAnalytics($request);

$this->assertNotNull($result['Result'])

        echo "  ✅ E2E: Get blockchain analytics and statistics\n";
    }
    public function testGet_block(): void
    {
        $requestJson = '{
  "BlockNumber": 1,
  "Blockchain": "${CIRCULAR_TEST_BLOCKCHAIN}",
  "Version": "1.0.8"
}';
        $requestJson = str_replace('${CIRCULAR_TEST_ADDRESS}', getenv('CIRCULAR_TEST_ADDRESS') ?: '', $requestJson);
        $requestJson = str_replace('${CIRCULAR_TEST_BLOCKCHAIN}', getenv('CIRCULAR_TEST_BLOCKCHAIN') ?: 'MainNet', $requestJson);
        $request = json_decode($requestJson, true);

        $result = $this->api->getBlock($request);

$this->assertNotNull($result['Result'])

        echo "  ✅ E2E: Retrieve specific block by number\n";
    }
    public function testGet_block_count(): void
    {
        $requestJson = '{
  "Blockchain": "${CIRCULAR_TEST_BLOCKCHAIN}",
  "Version": "1.0.8"
}';
        $requestJson = str_replace('${CIRCULAR_TEST_ADDRESS}', getenv('CIRCULAR_TEST_ADDRESS') ?: '', $requestJson);
        $requestJson = str_replace('${CIRCULAR_TEST_BLOCKCHAIN}', getenv('CIRCULAR_TEST_BLOCKCHAIN') ?: 'MainNet', $requestJson);
        $request = json_decode($requestJson, true);

        $result = $this->api->getBlockCount($request);

$this->assertNotNull($result['Result'])

        echo "  ✅ E2E: Get current block count from blockchain\n";
    }
    public function testGet_block_range(): void
    {
        $requestJson = '{
  "Blockchain": "${CIRCULAR_TEST_BLOCKCHAIN}",
  "EndBlock": 10,
  "StartBlock": 1,
  "Version": "1.0.8"
}';
        $requestJson = str_replace('${CIRCULAR_TEST_ADDRESS}', getenv('CIRCULAR_TEST_ADDRESS') ?: '', $requestJson);
        $requestJson = str_replace('${CIRCULAR_TEST_BLOCKCHAIN}', getenv('CIRCULAR_TEST_BLOCKCHAIN') ?: 'MainNet', $requestJson);
        $request = json_decode($requestJson, true);

        $result = $this->api->getBlockRange($request);

$this->assertNotNull($result['Result'])

        echo "  ✅ E2E: Retrieve range of blocks\n";
    }

    // Domain API E2E Tests (Read-Only)
    public function testGet_domain(): void
    {
        $requestJson = '{
  "Blockchain": "${CIRCULAR_TEST_BLOCKCHAIN}",
  "Domain": "test.circular",
  "Version": "1.0.8"
}';
        $requestJson = str_replace('${CIRCULAR_TEST_ADDRESS}', getenv('CIRCULAR_TEST_ADDRESS') ?: '', $requestJson);
        $requestJson = str_replace('${CIRCULAR_TEST_BLOCKCHAIN}', getenv('CIRCULAR_TEST_BLOCKCHAIN') ?: 'MainNet', $requestJson);
        $request = json_decode($requestJson, true);

        $result = $this->api->getDomain($request);

$this->assertNotNull($result['Result'])

        echo "  ✅ E2E: Resolve domain name to wallet address\n";
    }

    // Contract API E2E Tests (Read-Only)
    public function testTest_contract(): void
    {
        $requestJson = '{
  "Blockchain": "${CIRCULAR_TEST_BLOCKCHAIN}",
  "ContractAddress": "0x0000000000000000000000000000000000000000000000000000000000000000",
  "Method": "testMethod",
  "Parameters": "{}",
  "Version": "1.0.8"
}';
        $requestJson = str_replace('${CIRCULAR_TEST_ADDRESS}', getenv('CIRCULAR_TEST_ADDRESS') ?: '', $requestJson);
        $requestJson = str_replace('${CIRCULAR_TEST_BLOCKCHAIN}', getenv('CIRCULAR_TEST_BLOCKCHAIN') ?: 'MainNet', $requestJson);
        $request = json_decode($requestJson, true);

        $result = $this->api->testContract($request);

$this->assertNotNull($result['Result'])

        echo "  ✅ E2E: Test smart contract execution (simulation)\n";
    }

    // ========================================
    // Write Operations E2E Tests (LIVE BLOCKCHAIN)
    // ========================================

public function testRegister_wallet(): void
{
    $privateKey = getenv('CIRCULAR_PRIVATE_KEY');
    $publicKey = $this->api->getPublicKey($privateKey);
    $address = $this->api->hashString($publicKey);

    $blockchain = getenv('CIRCULAR_TEST_BLOCKCHAIN') ?: '0x8a20baa40c45dc5055aeb26197c203e576ef389d9acb171bd62da11dc5ad72b2';
    $timestamp = gmdate('Y:m:d-H:i:s');

            $accountName = 'E2E-Test-Wallet-' . time();
            $signaturePayload = $blockchain . $accountName . $publicKey;
            $signature = $this->api->signMessage($signaturePayload, $privateKey);
    
            $request = [
                'Blockchain' => $blockchain,
                'AccountName' => $accountName,
                'PublicKey' => $publicKey,
                'Signature' => $signature,
                'Version' => '1.0.8'
            ];
    
            echo "  📝 Registering wallet: $accountName\n";
            $result = $this->api->registerWallet($request);
    
    $this->assertEquals(200, $result['Result'])
    $this->assertNotNull($result['Response']['WalletAddress'])
    $this->assertMatchesRegularExpression('/^(0x)?[0-9a-fA-F]+$/', $result['Response']['WalletAddress'])
    $this->assertNotNull($result['Response']['TransactionID'])
    
            echo "  ✅ Wallet registered successfully\n";
            echo "  📍 Wallet Address: " . $result['Response']['WalletAddress'] . "\n";
            echo "  🔗 Transaction ID: " . $result['Response']['TransactionID'] . "\n";
}

public function testCertify_data(): void
{
    $privateKey = getenv('CIRCULAR_PRIVATE_KEY');
    $publicKey = $this->api->getPublicKey($privateKey);
    $address = $this->api->hashString($publicKey);

    $blockchain = getenv('CIRCULAR_TEST_BLOCKCHAIN') ?: '0x8a20baa40c45dc5055aeb26197c203e576ef389d9acb171bd62da11dc5ad72b2';
    $timestamp = gmdate('Y:m:d-H:i:s');

            $data = 'E2E Test Data Certification ' . time();
            $signaturePayload = $blockchain . $address . $address . '0' . 'C_TYPE_CERTIFICATE' . $timestamp . '' . $data;
            $signature = $this->api->signMessage($signaturePayload, $privateKey);
    
            $request = [
                'Blockchain' => $blockchain,
                'FromWallet' => $address,
                'ToWallet' => $address,
                'Amount' => '0',
                'TransactionType' => 'C_TYPE_CERTIFICATE',
                'Timestamp' => $timestamp,
                'Voucher' => '',
                'Data' => $data,
                'Signature' => $signature,
                'Version' => '1.0.8'
            ];
    
            echo "  📝 Certifying data on blockchain...\n";
            $result = $this->api->sendTransaction($request);
    
    $this->assertEquals(200, $result['Result'])
    $this->assertNotNull($result['Response']['TransactionID'])
    $this->assertMatchesRegularExpression('/^(0x)?[0-9a-fA-F]+$/', $result['Response']['TransactionID'])
    
            echo "  ✅ Data certified successfully\n";
            echo "  🔗 Transaction ID: " . $result['Response']['TransactionID'] . "\n";
            echo "  📄 Certified data: $data\n";
}

public function testCall_contract(): void
{
    $privateKey = getenv('CIRCULAR_PRIVATE_KEY');
    $publicKey = $this->api->getPublicKey($privateKey);
    $address = $this->api->hashString($publicKey);

    $blockchain = getenv('CIRCULAR_TEST_BLOCKCHAIN') ?: '0x8a20baa40c45dc5055aeb26197c203e576ef389d9acb171bd62da11dc5ad72b2';
    $timestamp = gmdate('Y:m:d-H:i:s');

            $request = [
                'Blockchain' => $blockchain,
                'From' => $address,
                'Address' => '0x0000000000000000000000000000000000000000000000000000000000000000',
                'Request' => '0x74657374',
                'Timestamp' => $timestamp,
                'Version' => '1.0.8'
            ];
    
            echo "  📝 Calling smart contract function...\n";
            $result = $this->api->callContract($request);
    
    $this->assertNotNull($result['Result'])
    
            echo "  ✅ Contract call executed (may have failed if contract doesn't exist)\n";
            echo "  📊 Result: " . $result['Result'] . "\n";
}
}