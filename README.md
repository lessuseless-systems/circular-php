# Circular Protocol - PHP SDK

[![Packagist Version](https://img.shields.io/packagist/v/circular/circular-protocol.svg)](https://packagist.org/packages/circular/circular-protocol)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

The **Circular Protocol PHP SDK** is the official PHP library for seamless integration with the Circular blockchain ecosystem. This open-source SDK provides a comprehensive suite of tools for efficient and secure interaction with blockchain networks, managing wallets, assets, smart contracts, and more.

## 🔥 Key Features

- **Blockchain Interaction**: Connect and interact with Circular's blockchain networks
- **Smart Contracts**: Deploy, test, and interact with smart contracts
- **Wallet Management**: Create, retrieve, and manage blockchain wallets with balance tracking
- **Asset Management**: Issue and manage assets, handle transfers, and retrieve supply information
- **Domain Management**: Resolve blockchain domain names to wallet addresses
- **Transaction Management**: Send transactions, track status, and search the blockchain
- **Analytics**: Access blockchain performance data and insights
- **Cryptographic Helpers**: Built-in utilities for key generation, signing, and hashing
- **PHP 8.0+ Support**: Compatible with PHP 8.0, 8.1, 8.2, and 8.3
- **PSR-4 Autoloading**: Follows PHP-FIG standards for modern PHP development

```bash
composer require circular/circular-protocol
```

## 🚀 Quick Start

```php
<?php

require_once 'vendor/autoload.php';

use Circular\Protocol\CircularProtocolAPI;
use Circular\Protocol\CircularProtocolException;

// Initialize the API client
$api = new CircularProtocolAPI(
    'https://nag.circularlabs.io/NAG.php?cep=',
    'your-api-key' // Optional
);

try {
    // Check if a wallet exists
    // Note: Version auto-injected, 0x prefix auto-stripped
    $result = $api->checkWallet([
        'Address' => '0xd55872dbe508fd27445889b9d81bbc9411bb0f1353153a249f2fb34ef2690310',
        'Blockchain' => 'MainNet'
    ]);

    echo "Wallet exists: " . $result['Response'] . "\n";
} catch (CircularProtocolException $e) {
    echo "API Error: " . $e->getMessage() . "\n";
}
```

### ✨ Auto-Preprocessing (v1.0.9+)

The SDK automatically handles common parameter transformations:

```php
// Auto-strips 0x prefix from hex fields
$api->checkWallet(['Address' => '0xabc...']); // Processed as 'abc...'

// Auto-injects Version if not provided
$api->getWallet(['Address' => '...']); // Version: '1.0.9' added automatically

// Configure auto-preprocessing (enabled by default)
$api->setAutoPreprocess(false); // Disable if needed
$api->setDefaultVersion('2.0.0'); // Change default version
```

## 📜 API Reference

The Circular Protocol PHP SDK provides **39 methods** across multiple categories for comprehensive blockchain interaction.

### Wallet Operations (5 methods)

- **`checkWallet()`** - Verify wallet existence on the blockchain
- **`getWallet()`** - Retrieve complete wallet details and metadata
- **`getLatestTransactions()`** - Get recent wallet activity and transaction history
- **`getWalletBalance()`** - Query current wallet balance across assets
- **`getWalletNonce()`** - Get transaction nonce for the wallet

### Transaction Operations (6 methods)

- **`sendTransaction()`** - Submit new transaction to the blockchain
- **`getPendingTransaction()`** - Check transaction status in the mempool
- **`getTransactionById()`** - Query transaction by unique identifier
- **`getTransactionByNode()`** - Query transactions by validator node
- **`getTransactionByAddress()`** - Query all transactions for a wallet address
- **`getTransactionByDate()`** - Query transactions within a date range

### Block Operations (4 methods)

- **`getBlock()`** - Retrieve block data by block number or hash
- **`getBlockRange()`** - Query multiple blocks within a range
- **`getBlockCount()`** - Get current blockchain height (latest block number)
- **`getAnalytics()`** - Retrieve blockchain performance metrics and analytics

### Contract Operations (2 methods)

- **`testContract()`** - Validate smart contract logic before deployment
- **`callContract()`** - Execute smart contract function call

### Asset Operations (4 methods)

- **`getAssetList()`** - List all available assets on the blockchain
- **`getAsset()`** - Get detailed asset information and metadata
- **`getAssetSupply()`** - Query total and circulating supply for an asset
- **`getVoucher()`** - Retrieve voucher data and redemption details

### Domain Operations (1 method)

- **`getDomain()`** - Query blockchain domain registry (resolve domain to address)

### Network Operations (1 method)

- **`getBlockchains()`** - List all supported blockchain networks

---

### Cryptographic Helpers (5 methods)

- **`signMessage()`** - Generate ECDSA secp256k1 signatures (DER format)
- **`verifySignature()`** - Verify message signatures against public keys
- **`getPublicKey()`** - Derive public key from private key (128 hex characters, uncompressed, no 0x04 prefix)
- **`hashString()`** - Generate SHA-256 hash of string input
- **`getFormattedTimestamp()`** - Get current UTC timestamp in Circular Protocol format (`YYYY:MM:DD-HH:mm:ss`)

**Implementation Details:**
- **TypeScript/JavaScript**: `crypto-browserify` (browser-compatible)
- **Python**: `ecdsa` + `hashlib` (standard library)
- **Java**: Bouncy Castle library for secp256k1
- **PHP**: `phpseclib3` elliptic curve cryptography
- **Go**: `btcsuite/btcd/btcec/v2` secp256k1
- **Dart**: `pointycastle` package

---

### Encoding Helpers (4 methods)

- **`hexFix()`** - Normalize hex strings (remove `0x` prefix if present)
- **`stringToHex()`** - Convert UTF-8 string to hexadecimal encoding
- **`hexToString()`** - Convert hexadecimal string to UTF-8
- **`padNumber()`** - Zero-pad single-digit numbers (e.g., `5` → `"05"`)

---

### Advanced Helpers (3 methods)

- **`getError()`** - Retrieve last error message from SDK
- **`handleError()`** - Internal error tracking and logging
- **`getTransactionOutcome()`** - Poll for transaction confirmation with automatic retries

**Transaction Polling Behavior:**
- Checks transaction status every **5 seconds** (configurable via `intervalSec`)
- Returns successfully when transaction has `BlockNumber > 0` (confirmed)
- Throws timeout error after **120 seconds** (configurable via `timeoutSec`)
- Handles "pending" status gracefully with automatic retries
- Distinguishes between temporary "pending" and permanent errors

---

### Convenience Methods (1 method)

- **`registerWallet()`** - Simplified wallet registration (wraps `sendTransaction()`)

**Implementation:**
- Automatically derives `From` and `To` addresses via `hashString(publicKey)`
- Constructs transaction payload: `{"Action": "CP_WALLET", "PublicKey": "..."}`
- Sets default values: `Nonce="00000000"`, `Type="C"`, `Signature="0000..."`
- Calculates transaction ID as SHA-256 hash of transaction fields
- Returns same response structure as `sendTransaction`

---

## 📊 Total Methods: 39

- **23** API Endpoint Methods
- **5** Cryptographic Helpers
- **4** Encoding Helpers
- **3** Advanced Helpers
- **3** Configuration Methods (getNagUrl, setNagUrl, getNagKey, setNagKey, setHeader, etc.)
- **1** Convenience Method

> **Note**: For detailed parameter types, response structures, and advanced usage examples, refer to the **[PHP SDK Documentation](https://circular-protocol.gitbook.io/circular-sdk/api-docs/php)**.

## 🤝 Contributing

Contributions are welcome! Please see the [CONTRIBUTING.md](https://github.com/circular-protocol/circular-canonical/blob/main/CONTRIBUTING.md) file in the canonical repository for guidelines.

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 📚 Resources

- **[PHP SDK Documentation](https://circular-protocol.gitbook.io/circular-sdk/api-docs/php)** - Complete API reference
- **[Circular Protocol Docs](https://circular-protocol.gitbook.io)** - Protocol documentation
- **[Circular Canonical](https://github.com/circular-protocol/circular-canonical)** - Single source of truth
- **[Package on Packagist](https://packagist.org/packages/circular/circular-protocol)** - Official PHP package

## ℹ️ About

**Version**: 1.0.9
**License**: MIT
**Repository**: [circular-php](https://github.com/circular-protocol/circular-php)

---

© 2025 Circular Global Ledgers, Inc. - Open source for private and commercial use