# Changelog

All notable changes to the Circular Protocol PHP SDK will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.9] - 2025-01-16

### Added
- **Auto-Preprocessing System** - Automatic parameter normalization and validation
  - Auto-strips `0x` prefix from all hex fields (Address, From, To, ID, Signature, etc.)
  - Auto-injects `Version` parameter if not provided (defaults to '1.0.9')
  - Configurable via `setAutoPreprocess(bool)` and `getAutoPreprocess()`
- **Helper-Specific Unit Tests** - Comprehensive test suite for all helper functions
  - `CircularProtocolHelpersTest.php` with 35+ test cases
  - Tests cryptographic functions (sign, verify, getPublicKey, hashString)
  - Tests encoding functions (hexFix, stringToHex, hexToString)
  - Tests edge cases and Unicode handling
- **PHPUnit Configuration** - `phpunit.xml` for test organization and coverage
  - Separate test suites: unit, integration, e2e
  - Coverage reporting configuration
  - Optimized test execution order
- **GitHub Actions CI/CD Workflow** - Automated testing and deployment
  - Multi-version PHP testing (8.0, 8.1, 8.2, 8.3)
  - Code quality checks (PHPStan Level 8, CodeSniffer)
  - E2E testing with environment variables
  - Automated Packagist publishing
- **PHPStan Level 8 Compliance** - Strictest static analysis level with 0 errors
  - Complete type hints for all method parameters and return values
  - Proper handling of functions that can return false (hex2bin, getenv, etc.)
  - Array type specifications: `array<string, mixed>` for all associative arrays
  - Removed all unused code and variables
- **Configuration Methods** for auto-preprocessing
  - `setDefaultVersion(string)` / `getDefaultVersion()`
  - `setAutoPreprocess(bool)` / `getAutoPreprocess()`
- Missing `phpseclib3` imports (`EC`, `BigInteger`) for cryptographic operations
- `CHANGELOG.md` - Version history documentation
- `CONTRIBUTING.md` - Development and contribution guidelines
- `CODE_OF_CONDUCT.md` - Community standards (Contributor Covenant v2.1)
- `SECURITY.md` - Vulnerability reporting procedures
- `AGENTS.md` - Architecture guide for AI coding assistants
- `LICENSE` - MIT License full text

### Changed
- **BREAKING: `registerWallet()` signature changed** to match API specification
  - Old: `registerWallet(string $blockchain, string $publicKey): array`
  - New: `registerWallet(array $request): array`
  - Now accepts full request with Blockchain, AccountName, PublicKey, Signature, Version
- **`hexFix()` visibility changed** from private to public for developer access
- Updated all method names to follow PHP naming conventions (camelCase)
  - `getTransactionbyID()` → `getTransactionById()`
  - `getTransactionbyNode()` → `getTransactionByNode()`
  - `getTransactionbyAddress()` → `getTransactionByAddress()`
  - `getTransactionbyDate()` → `getTransactionByDate()`
  - `register_wallet()` → `registerWallet()`
  - `addTransaction()` → `sendTransaction()`
- Default API version updated from '1.0.8' to '1.0.9'
- Updated README.md to reflect correct method names with parentheses
- Enhanced documentation for better IDE support

### Fixed
- **Critical: `registerWallet()` method signature** - Now matches actual API requirements
  - Previous implementation didn't accept AccountName and Signature parameters
  - Now properly routes to 'RegisterWallet' endpoint with full request object
- Critical: Added missing imports for phpseclib3 cryptographic classes
- Fixed endpoint naming for `getDomain()`: 'GetDomain' → 'ResolveDomain'
- Fixed endpoint naming for `getBlockCount()`: 'GetBlockCount' → 'GetBlockHeight'
- Fixed inconsistent method naming across codebase
- Updated test files to use correct method names
- Updated `.gitignore` to allow committing `phpunit.xml` configuration

## [1.0.8] - 2025-01-01

### Added
- Initial release with 39 methods across multiple categories
- Wallet operations (5 methods)
- Transaction operations (6 methods)
- Block operations (4 methods)
- Contract operations (2 methods)
- Asset operations (4 methods)
- Domain operations (1 method)
- Network operations (1 method)
- Cryptographic helpers (5 methods)
- Encoding helpers (4 methods)
- Advanced helpers (3 methods)
- Configuration methods (4 methods)
- Convenience method: `registerWallet()`

### Features
- PHP 8.0+ support with type hints
- PSR-4 autoloading
- Exception handling with `CircularProtocolException`
- secp256k1 signing and verification using phpseclib3
- SHA-256 hashing utilities
- Hex encoding/decoding helpers
- Transaction polling with `getTransactionOutcome()`
- Configurable NAG endpoint and API key
- Comprehensive integration and E2E test suites

---

## Version History Summary

- **1.0.9** - Documentation, naming fixes, and critical import bug fix
- **1.0.8** - Initial release with full API coverage

---

## Upgrade Guide

### From 1.0.8 to 1.0.9

**Breaking Changes:** None - This release is 100% backward compatible.

**Method Renames:** The following methods have been renamed to follow PHP conventions. Old names are no longer available:

- `addTransaction()` → `sendTransaction()`
- `getTransactionbyID()` → `getTransactionById()`
- `getTransactionbyNode()` → `getTransactionByNode()`
- `getTransactionbyAddress()` → `getTransactionByAddress()`
- `getTransactionbyDate()` → `getTransactionByDate()`
- `register_wallet()` → `registerWallet()`

**Migration Example:**

```php
// Before (v1.0.8)
$result = $api->addTransaction($request);
$tx = $api->getTransactionbyID($request);

// After (v1.0.9)
$result = $api->sendTransaction($request);
$tx = $api->getTransactionById($request);
```

**Bug Fix:** If you were experiencing cryptographic errors with `signMessage()`, `verifySignature()`, or `getPublicKey()`, these are now fixed with proper imports.

---

For complete documentation, visit [GitBook Documentation](https://circular-protocol.gitbook.io/circular-sdk/api-docs/php).
