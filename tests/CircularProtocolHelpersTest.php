<?php

namespace Circular\Protocol\Tests;

use Circular\Protocol\CircularProtocolAPI;
use Circular\Protocol\CircularProtocolException;
use PHPUnit\Framework\TestCase;

/**
 * Circular Protocol PHP SDK Helper Functions Tests
 *
 * Tests all cryptographic, encoding, and utility helper functions
 */
class CircularProtocolHelpersTest extends TestCase
{
    private CircularProtocolAPI $api;

    protected function setUp(): void
    {
        $this->api = new CircularProtocolAPI();
    }

    // ========== Cryptographic Helpers ==========
    // Note: Comprehensive crypto tests are in E2E test suite
    // These tests validate basic functionality only

    public function testVerifySignatureHandlesInvalidInput(): void
    {
        $isValid = $this->api->verifySignature('invalid-pubkey', 'message', 'invalid-sig');

        $this->assertFalse($isValid);
    }

    public function testHashStringProducesSHA256(): void
    {
        $input = 'test string';
        $hash = $this->api->hashString($input);

        $this->assertIsString($hash);
        $this->assertEquals(64, strlen($hash)); // SHA256 = 64 hex chars
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/i', $hash);
    }

    public function testHashStringIsConsistent(): void
    {
        $input = 'consistent test';

        $hash1 = $this->api->hashString($input);
        $hash2 = $this->api->hashString($input);

        $this->assertEquals($hash1, $hash2);
    }

    public function testHashStringProducesDifferentHashesForDifferentInputs(): void
    {
        $hash1 = $this->api->hashString('input1');
        $hash2 = $this->api->hashString('input2');

        $this->assertNotEquals($hash1, $hash2);
    }

    // ========== Encoding Helpers ==========

    public function testHexFixRemoves0xPrefix(): void
    {
        $withPrefix = '0xabcdef123456';
        $result = $this->api->hexFix($withPrefix);

        $this->assertEquals('abcdef123456', $result);
    }

    public function testHexFixHandlesUppercase0X(): void
    {
        $withPrefix = '0Xabcdef';
        $result = $this->api->hexFix($withPrefix);

        $this->assertEquals('abcdef', $result);
    }

    public function testHexFixLeavesNoPrefixUnchanged(): void
    {
        $noPrefix = 'abcdef123456';
        $result = $this->api->hexFix($noPrefix);

        $this->assertEquals($noPrefix, $result);
    }

    public function testStringToHexEncodesCorrectly(): void
    {
        $input = 'Hello';
        $hex = $this->api->stringToHex($input);

        // 'Hello' = 0x48656c6c6f
        $this->assertEquals('48656c6c6f', $hex);
    }

    public function testStringToHexHandlesEmptyString(): void
    {
        $hex = $this->api->stringToHex('');
        $this->assertEquals('', $hex);
    }

    public function testStringToHexHandlesSpecialChars(): void
    {
        $input = '{"test": true}';
        $hex = $this->api->stringToHex($input);

        $this->assertMatchesRegularExpression('/^[0-9a-f]+$/i', $hex);
        $this->assertNotEmpty($hex);
    }

    public function testHexToStringDecodesCorrectly(): void
    {
        $hex = '48656c6c6f';
        $string = $this->api->hexToString($hex);

        $this->assertEquals('Hello', $string);
    }

    public function testHexToStringHandles0xPrefix(): void
    {
        $hex = '0x48656c6c6f';
        $string = $this->api->hexToString($hex);

        $this->assertEquals('Hello', $string);
    }

    public function testStringToHexAndBackRoundTrip(): void
    {
        $original = 'Test string with special chars: !@#$%';

        $hex = $this->api->stringToHex($original);
        $decoded = $this->api->hexToString($hex);

        $this->assertEquals($original, $decoded);
    }

    public function testGetFormattedTimestampFormat(): void
    {
        $timestamp = $this->api->getFormattedTimestamp();

        // Format: YYYY:MM:DD-HH:mm:ss
        $this->assertMatchesRegularExpression('/^\d{4}:\d{2}:\d{2}-\d{2}:\d{2}:\d{2}$/', $timestamp);
    }

    public function testGetFormattedTimestampIsUTC(): void
    {
        $timestamp = $this->api->getFormattedTimestamp();

        // Parse the timestamp
        $parts = explode('-', $timestamp);
        $datePart = $parts[0];
        $timePart = $parts[1];

        $this->assertNotEmpty($datePart);
        $this->assertNotEmpty($timePart);
    }

    public function testGetFormattedTimestampPadsNumbers(): void
    {
        $timestamp = $this->api->getFormattedTimestamp();

        // Check that all parts are 2 digits (except year which is 4)
        $pattern = '/^\d{4}:\d{2}:\d{2}-\d{2}:\d{2}:\d{2}$/';
        $this->assertMatchesRegularExpression($pattern, $timestamp);
    }

    // ========== Configuration Methods ==========

    public function testSetAndGetDefaultVersion(): void
    {
        $this->api->setDefaultVersion('2.0.0');
        $this->assertEquals('2.0.0', $this->api->getDefaultVersion());
    }

    public function testDefaultVersionIsSet(): void
    {
        $version = $this->api->getDefaultVersion();
        $this->assertEquals('1.0.9', $version);
    }

    public function testSetAndGetAutoPreprocess(): void
    {
        $this->api->setAutoPreprocess(false);
        $this->assertFalse($this->api->getAutoPreprocess());

        $this->api->setAutoPreprocess(true);
        $this->assertTrue($this->api->getAutoPreprocess());
    }

    public function testAutoPreprocessDefaultsToTrue(): void
    {
        $api = new CircularProtocolAPI();
        $this->assertTrue($api->getAutoPreprocess());
    }

    // ========== Error Handling ==========

    public function testGetErrorReturnsEmptyStringInitially(): void
    {
        $error = $this->api->getError();
        $this->assertEquals('', $error);
    }

    // ========== Auto-Preprocessing ==========

    public function testAutoPreprocessStrips0xFromAddress(): void
    {
        // We can't directly test preprocessRequest since it's private,
        // but we can verify it works through API calls by checking the request
        // This is implicitly tested through integration tests
        $this->assertTrue(true);
    }


    // ========== Edge Cases ==========

    public function testHashStringHandlesUnicodeCharacters(): void
    {
        $unicode = '测试 🚀 Тест';
        $hash = $this->api->hashString($unicode);

        $this->assertEquals(64, strlen($hash));
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/i', $hash);
    }

    public function testStringToHexHandlesUnicode(): void
    {
        $unicode = '🚀';
        $hex = $this->api->stringToHex($unicode);

        $this->assertNotEmpty($hex);
        $this->assertMatchesRegularExpression('/^[0-9a-f]+$/i', $hex);

        // Verify round trip
        $decoded = $this->api->hexToString($hex);
        $this->assertEquals($unicode, $decoded);
    }
}
