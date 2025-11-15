<?php

namespace CircularProtocol\Tests;

use PHPUnit\Framework\TestCase;
use CircularProtocol\CircularProtocolAPI;

/**
 * Circular Protocol PHP SDK Integration Tests
 * Generated from tests/L3-integration/integration-tests.test.ncl
 *
 * These tests validate SDK functionality against a mock API server.
 * They test real HTTP requests, response parsing, and error handling.
 *
 * Requirements:
 * - Mock server running on http://localhost:8080
 * - Start with: python3 dist/tests/mock-server.py
 *
 * Run tests:
 *   composer test:integration
 *   or: vendor/bin/phpunit tests/CircularProtocolIntegrationTest.php
 */
class CircularProtocolIntegrationTest extends TestCase
{
    private static CircularProtocolAPI $api;
    private const API_URL = 'http://localhost:8080';
    private const API_VERSION = '1.0.8';

    public static function setUpBeforeClass(): void
    {
        $apiUrl = getenv('CIRCULAR_API_URL') ?: self::API_URL;
        self::$api = new CircularProtocolAPI($apiUrl);
    }

    protected function setUp(): void
    {
        $this->api = self::$api;
    }

    // ========== Network API ==========
    /**
     * @test
     * @group integration
     * Should list supported blockchains
     */
    public function test_get_blockchains(): void
    {
        $request = [
'Version' => '1.0.8',
        ];

        $result = $this->api->getBlockchains($request);

$this->assertEquals(200, $result['Result']);
$this->assertIsArray($result['Response']['blockchains']);
$this->assertContains('MainNet', $result['Response']['blockchains']);

        echo "  ✅ Should list supported blockchains\n";
    }

    // ========== Wallet API ==========
    /**
     * @test
     * @group integration
     * Should successfully check if wallet exists
     */
    public function test_check_wallet(): void
    {
        $request = [
'Address' => '0xbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb',
'Blockchain' => '"MainNet"',
'Version' => '1.0.8',
        ];

        $result = $this->api->checkWallet($request);

$this->assertEquals(200, $result['Result']);
$this->assertEquals(true, $result['Response']['exists']);

        echo "  ✅ Should successfully check if wallet exists\n";
    }
    /**
     * @test
     * @group integration
     * Should fetch recent transactions for wallet
     */
    public function test_get_latest_transactions(): void
    {
        $request = [
'Address' => '0xbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb',
'Blockchain' => '"MainNet"',
'Limit' => 10,
'Version' => '1.0.8',
        ];

        $result = $this->api->getLatestTransactions($request);

$this->assertEquals(200, $result['Result']);
$this->assertIsArray($result['Response']['transactions']);

        echo "  ✅ Should fetch recent transactions for wallet\n";
    }
    /**
     * @test
     * @group integration
     * Should retrieve wallet details
     */
    public function test_get_wallet(): void
    {
        $request = [
'Address' => '0xbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb',
'Blockchain' => '"MainNet"',
'Version' => '1.0.8',
        ];

        $result = $this->api->getWallet($request);

$this->assertEquals(200, $result['Result']);
$this->assertNotNull($result['Response']['address']);

        echo "  ✅ Should retrieve wallet details\n";
    }
    /**
     * @test
     * @group integration
     * Should get wallet balance for specific asset
     */
    public function test_get_wallet_balance(): void
    {
        $request = [
'Address' => '0xbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb',
'Asset' => '0xC123',
'Blockchain' => '"MainNet"',
'Version' => '1.0.8',
        ];

        $result = $this->api->getWalletBalance($request);

$this->assertEquals(200, $result['Result']);
$this->assertNotNull($result['Response']['balance']);

        echo "  ✅ Should get wallet balance for specific asset\n";
    }
    /**
     * @test
     * @group integration
     * Should get current wallet nonce
     */
    public function test_get_wallet_nonce(): void
    {
        $request = [
'Address' => '0xbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb',
'Blockchain' => '"MainNet"',
'Version' => '1.0.8',
        ];

        $result = $this->api->getWalletNonce($request);

$this->assertEquals(200, $result['Result']);
$this->assertGreaterThanOrEqual(0, $result['Response']['nonce']);

        echo "  ✅ Should get current wallet nonce\n";
    }

    // ========== Transaction API ==========
    /**
     * @test
     * @group integration
     * Should submit a transaction to blockchain
     */
    public function test_add_transaction(): void
    {
        $request = [
'From' => '0xbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb',
'ID' => '0xaabbccdd11223344',
'Nonce' => 1,
'Payload' => '0x1234',
'Signature' => '0xsignature',
'Timestamp' => '1234567890',
'To' => '0xcccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccc',
'Type' => 'transfer',
'Version' => '1.0.8',
        ];

        $result = $this->api->addTransaction($request);

$this->assertEquals(200, $result['Result']);
$this->assertNotNull($result['Response']['transaction_id']);

        echo "  ✅ Should submit a transaction to blockchain\n";
    }
    /**
     * @test
     * @group integration
     * Should get pending transactions
     */
    public function test_get_pending_transaction(): void
    {
        $request = [
'Blockchain' => '"MainNet"',
'Version' => '1.0.8',
        ];

        $result = $this->api->getPendingTransaction($request);

$this->assertEquals(200, $result['Result']);
$this->assertIsArray($result['Response']['transactions']);

        echo "  ✅ Should get pending transactions\n";
    }
    /**
     * @test
     * @group integration
     * Should get transactions by address
     */
    public function test_get_transaction_by_address(): void
    {
        $request = [
'Address' => '0xbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb',
'Blockchain' => '"MainNet"',
'Version' => '1.0.8',
        ];

        $result = $this->api->getTransactionbyAddress($request);

$this->assertEquals(200, $result['Result']);
$this->assertIsArray($result['Response']['transactions']);

        echo "  ✅ Should get transactions by address\n";
    }
    /**
     * @test
     * @group integration
     * Should get transactions by date range
     */
    public function test_get_transaction_by_date(): void
    {
        $request = [
'Blockchain' => '"MainNet"',
'EndDate' => '2024-12-31',
'StartDate' => '2024-01-01',
'Version' => '1.0.8',
        ];

        $result = $this->api->getTransactionbyDate($request);

$this->assertEquals(200, $result['Result']);
$this->assertIsArray($result['Response']['transactions']);

        echo "  ✅ Should get transactions by date range\n";
    }
    /**
     * @test
     * @group integration
     * Should get transaction by ID
     */
    public function test_get_transaction_by_id(): void
    {
        $request = [
'Blockchain' => '"MainNet"',
'TransactionID' => '0xaabbccdd11223344',
'Version' => '1.0.8',
        ];

        $result = $this->api->getTransactionbyID($request);

$this->assertEquals(200, $result['Result']);
$this->assertNotNull($result['Response']['transaction']);

        echo "  ✅ Should get transaction by ID\n";
    }
    /**
     * @test
     * @group integration
     * Should get transactions by node
     */
    public function test_get_transaction_by_node(): void
    {
        $request = [
'Blockchain' => '"MainNet"',
'Node' => '0xnode123',
'Version' => '1.0.8',
        ];

        $result = $this->api->getTransactionbyNode($request);

$this->assertEquals(200, $result['Result']);
$this->assertIsArray($result['Response']['transactions']);

        echo "  ✅ Should get transactions by node\n";
    }

    // ========== Asset API ==========
    /**
     * @test
     * @group integration
     * Should get asset details
     */
    public function test_get_asset(): void
    {
        $request = [
'Asset' => '0xC123',
'Blockchain' => '"MainNet"',
'Version' => '1.0.8',
        ];

        $result = $this->api->getAsset($request);

$this->assertEquals(200, $result['Result']);
$this->assertNotNull($result['Response']['asset']);

        echo "  ✅ Should get asset details\n";
    }
    /**
     * @test
     * @group integration
     * Should get list of all assets
     */
    public function test_get_asset_list(): void
    {
        $request = [
'Blockchain' => '"MainNet"',
'Version' => '1.0.8',
        ];

        $result = $this->api->getAssetList($request);

$this->assertEquals(200, $result['Result']);
$this->assertIsArray($result['Response']['assets']);

        echo "  ✅ Should get list of all assets\n";
    }
    /**
     * @test
     * @group integration
     * Should get asset supply information
     */
    public function test_get_asset_supply(): void
    {
        $request = [
'Asset' => '0xC123',
'Blockchain' => '"MainNet"',
'Version' => '1.0.8',
        ];

        $result = $this->api->getAssetSupply($request);

$this->assertEquals(200, $result['Result']);
$this->assertNotNull($result['Response']['total_supply']);

        echo "  ✅ Should get asset supply information\n";
    }
    /**
     * @test
     * @group integration
     * Should get voucher details
     */
    public function test_get_voucher(): void
    {
        $request = [
'Blockchain' => '"MainNet"',
'Version' => '1.0.8',
'VoucherID' => '0xvoucher123',
        ];

        $result = $this->api->getVoucher($request);

$this->assertEquals(200, $result['Result']);
$this->assertNotNull($result['Response']['voucher']);

        echo "  ✅ Should get voucher details\n";
    }

    // ========== Block API ==========
    /**
     * @test
     * @group integration
     * Should get blockchain analytics
     */
    public function test_get_analytics(): void
    {
        $request = [
'Blockchain' => '"MainNet"',
'Version' => '1.0.8',
        ];

        $result = $this->api->getAnalytics($request);

$this->assertEquals(200, $result['Result']);
$this->assertNotNull($result['Response']['analytics']);

        echo "  ✅ Should get blockchain analytics\n";
    }
    /**
     * @test
     * @group integration
     * Should get block by number
     */
    public function test_get_block(): void
    {
        $request = [
'Block' => 12345,
'Blockchain' => '"MainNet"',
'Version' => '1.0.8',
        ];

        $result = $this->api->getBlock($request);

$this->assertEquals(200, $result['Result']);
$this->assertNotNull($result['Response']['block']);

        echo "  ✅ Should get block by number\n";
    }
    /**
     * @test
     * @group integration
     * Should get current blockchain height
     */
    public function test_get_block_count(): void
    {
        $request = [
'Blockchain' => '"MainNet"',
'Version' => '1.0.8',
        ];

        $result = $this->api->getBlockCount($request);

$this->assertEquals(200, $result['Result']);
$this->assertGreaterThan(0, $result['Response']['count']);

        echo "  ✅ Should get current blockchain height\n";
    }
    /**
     * @test
     * @group integration
     * Should get range of blocks
     */
    public function test_get_block_range(): void
    {
        $request = [
'Blockchain' => '"MainNet"',
'EndBlock' => 10010,
'StartBlock' => 10000,
'Version' => '1.0.8',
        ];

        $result = $this->api->getBlockRange($request);

$this->assertEquals(200, $result['Result']);
$this->assertIsArray($result['Response']['blocks']);

        echo "  ✅ Should get range of blocks\n";
    }

    // ========== Smart Contract API ==========
    /**
     * @test
     * @group integration
     * Should call contract method
     */
    public function test_call_contract(): void
    {
        $request = [
'Blockchain' => '"MainNet"',
'ContractAddress' => '0xcontract123',
'Method' => 'balanceOf',
'Parameters' => '[
  "0xwallet123"
]',
'Version' => '1.0.8',
        ];

        $result = $this->api->callContract($request);

$this->assertEquals(200, $result['Result']);
$this->assertNotNull($result['Response']['result']);

        echo "  ✅ Should call contract method\n";
    }
    /**
     * @test
     * @group integration
     * Should test contract execution (dry run)
     */
    public function test_test_contract(): void
    {
        $request = [
'Blockchain' => '"MainNet"',
'ContractAddress' => '0xcontract123',
'Method' => 'transfer',
'Parameters' => '[
  "0xrecipient",
  "1000"
]',
'Version' => '1.0.8',
        ];

        $result = $this->api->testContract($request);

$this->assertEquals(200, $result['Result']);
$this->assertNotNull($result['Response']['result']);

        echo "  ✅ Should test contract execution (dry run)\n";
    }

    // ========== Domain API ==========
    /**
     * @test
     * @group integration
     * Should resolve domain to address
     */
    public function test_get_domain(): void
    {
        $request = [
'Blockchain' => '"MainNet"',
'Domain' => 'myname.circular',
'Version' => '1.0.8',
        ];

        $result = $this->api->getDomain($request);

$this->assertEquals(200, $result['Result']);
$this->assertNotNull($result['Response']['address']);

        echo "  ✅ Should resolve domain to address\n";
    }

    // ========== Error Handling ==========
    /**
     * @test
     * @group integration
     * Should handle network connection errors gracefully
     */
    public function test_connection_error(): void
    {
        $invalidApi = new CircularProtocolAPI('http://localhost:9999');

        $request = [
'Address' => '0xbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb',
'Blockchain' => '"MainNet"',
'Version' => '1.0.8',
        ];

        $this->expectException(\Exception::class);
        $invalidApi->checkWallet($request);

        echo "  ✅ Should handle network connection errors gracefully\n";
    }
    /**
     * @test
     * @group integration
     * Should handle invalid address gracefully
     */
    public function test_invalid_address(): void
    {
        $request = [
'Address' => 'invalid',
'Blockchain' => '"MainNet"',
'Version' => '1.0.8',
        ];

        $this->expectException(\Exception::class);
        $this->api->checkWallet($request);

        echo "  ✅ Should handle invalid address gracefully\n";
    }
}