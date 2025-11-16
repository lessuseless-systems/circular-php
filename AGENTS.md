# AGENTS.md - Architecture Guide for AI Coding Assistants

## Purpose

This document provides AI coding assistants (like Claude Code, GitHub Copilot, Cursor, etc.) with architectural context, design patterns, and implementation guidelines for the Circular Protocol PHP SDK. It serves as a comprehensive reference for maintaining consistency and quality when making code changes.

---

## Table of Contents

1. [Project Overview](#project-overview)
2. [Architecture](#architecture)
3. [Design Principles](#design-principles)
4. [Code Patterns](#code-patterns)
5. [Testing Strategy](#testing-strategy)
6. [Common Tasks](#common-tasks)
7. [Error Handling](#error-handling)
8. [Security Considerations](#security-considerations)
9. [Release Process](#release-process)

---

## Project Overview

### What is Circular Protocol PHP SDK?

The Circular Protocol PHP SDK is the official PHP library for interacting with the Circular blockchain ecosystem. It provides:

- **23 API endpoint methods** for blockchain operations
- **5 cryptographic helpers** for signing and verification
- **4 encoding helpers** for data transformation
- **3 advanced helpers** for error handling and transaction polling
- **4 configuration methods** for NAG endpoint setup
- **1 convenience method** for wallet registration

### Technology Stack

- **Language**: PHP 8.0+
- **Crypto Library**: phpseclib3 (secp256k1 ECDSA)
- **HTTP Client**: cURL extension
- **Testing**: PHPUnit 10.0+
- **Code Quality**: PHPStan, PHP_CodeSniffer (PSR-12)
- **Package Manager**: Composer

### Key Dependencies

```json
{
  "php": "^8.0",
  "phpseclib/phpseclib": "^3.0",
  "ext-curl": "*",
  "ext-json": "*"
}
```

---

## Architecture

### File Structure

```
circular-php/
├── src/
│   └── CircularProtocolAPI.php       # Main API class (863 lines)
├── tests/
│   ├── CircularProtocolUnitTest.php  # Unit tests with mocks
│   ├── CircularProtocolIntegrationTest.php  # API integration tests
│   └── CircularProtocolE2ETest.php   # End-to-end tests
├── composer.json                      # Package configuration
├── README.md                          # User documentation
├── CHANGELOG.md                       # Version history
├── CONTRIBUTING.md                    # Contributor guide
├── CODE_OF_CONDUCT.md                 # Community standards
├── SECURITY.md                        # Security policy
├── LICENSE                            # MIT License
└── AGENTS.md                          # This file
```

### Monolithic Design

Unlike the Python SDK (which uses modular architecture with 6 files), the PHP SDK uses a **monolithic single-class design**:

**Advantages:**
- Simple installation (single file import)
- Easy debugging (all code in one place)
- No namespace confusion
- Standard for PHP libraries (like Guzzle HTTP client)

**Trade-offs:**
- Larger file size (~863 lines)
- Less separation of concerns

**Design Decision:** Keep monolithic for v1.x releases. Consider modular refactor for v2.0 if file exceeds 2000 lines.

---

## Design Principles

### 1. Backward Compatibility First

**Rule:** Never break existing API contracts without major version bump.

```php
// ✅ Good: Adding optional parameters
public function sendTransaction(array $request, ?array $options = null): array

// ❌ Bad: Changing required parameters
public function sendTransaction(string $txId, array $data): array
```

### 2. Explicit Over Implicit

**Rule:** Be explicit in method signatures and return types.

```php
// ✅ Good: Explicit return type
public function getWallet(array $request): array

// ❌ Bad: No return type
public function getWallet(array $request)
```

### 3. Fail Fast, Fail Clearly

**Rule:** Validate input early, throw exceptions with clear messages.

```php
// ✅ Good: Early validation
if (empty($request['Address'])) {
    throw new CircularProtocolException('Address is required', 400, 'getWallet');
}

// ❌ Bad: Late failure with cryptic message
$result = $this->makeRequest('GetWallet', $request); // Fails with generic error
```

### 4. Single Responsibility

**Rule:** Each method does one thing well.

```php
// ✅ Good: Separate concerns
private function makeRequest(string $endpoint, array $data): array
private function validateRequest(array $request): void
private function handleError(\Throwable $error): void

// ❌ Bad: Method does everything
private function sendAndValidateAndLog(string $endpoint, array $data): array
```

### 5. Convention Over Configuration

**Rule:** Use sensible defaults, allow overrides.

```php
// ✅ Good: Default NAG URL with override option
public function __construct(?string $nagUrl = null, ?string $nagKey = null)
{
    $this->nagUrl = $nagUrl ?? 'https://nag.circularlabs.io/NAG.php?cep=';
}
```

---

## Code Patterns

### API Method Pattern

All API methods follow this pattern:

```php
/**
 * [Action] [resource]
 * [Detailed description of what this method does]
 *
 * @param array $request Request parameters
 * @return array Response with Result and Response fields
 * @throws CircularProtocolException If request fails
 */
public function methodName(array $request): array
{
    return $this->makeRequest('EndpointName', $request);
}
```

**Example:**

```php
/**
 * Get wallet information
 * Retrieves complete wallet information including balance and nonce.
 * Returns all wallet properties including current state on the blockchain.
 *
 * @param array $request Request parameters
 * @return array Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function getWallet(array $request): array
{
    return $this->makeRequest('GetWallet', $request);
}
```

### Helper Method Pattern

Helper methods are either `public` (part of SDK API) or `private` (internal use):

```php
// Public helpers - part of SDK API
public function hashString(string $str): string { ... }
public function signMessage(string $message, string $privateKey): string { ... }

// Private helpers - internal only
private function hexFix(string $hexString): string { ... }
private function padNumber(int $num): string { ... }
```

### Error Handling Pattern

```php
try {
    $result = $this->makeRequest('Endpoint', $data);
    return $result;
} catch (CircularProtocolException $e) {
    $this->handleError($e);
    throw $e;
}
```

### Request/Response Pattern

**All API methods:**
- Accept `array $request` parameter
- Return `array` with `Result` and `Response` fields
- Throw `CircularProtocolException` on failure

```php
// Request structure
$request = [
    'Address' => '0x...',
    'Blockchain' => 'MainNet',
    'Version' => '1.0.9'
];

// Response structure
$response = [
    'Result' => 200,
    'Response' => [ /* endpoint-specific data */ ]
];
```

---

## Testing Strategy

### Three-Tier Testing

**1. Unit Tests** (`CircularProtocolUnitTest.php`)
- Mock HTTP requests
- Test individual methods in isolation
- Fast execution (no network calls)

```php
public function testHashString(): void
{
    $api = new CircularProtocolAPI();
    $hash = $api->hashString('test');

    $this->assertEquals(
        '9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08',
        $hash
    );
}
```

**2. Integration Tests** (`CircularProtocolIntegrationTest.php`)
- Test against mock server (http://localhost:8080)
- Validate HTTP request/response handling
- Test error scenarios

```php
public function test_check_wallet(): void
{
    $result = $this->api->checkWallet([
        'Address' => '0xbbbb...',
        'Blockchain' => 'MainNet',
        'Version' => '1.0.9'
    ]);

    $this->assertEquals(200, $result['Result']);
}
```

**3. E2E Tests** (`CircularProtocolE2ETest.php`)
- Test against live NAG endpoints
- Require environment variables
- Run manually before releases

```php
// Requires: CIRCULAR_TEST_ADDRESS=0x...
public function testGet_wallet(): void
{
    $result = $this->api->getWallet([
        'Address' => getenv('CIRCULAR_TEST_ADDRESS'),
        'Blockchain' => 'MainNet',
        'Version' => '1.0.9'
    ]);

    $this->assertNotNull($result['Result']);
}
```

### Test Coverage Requirements

- **Minimum**: 80% code coverage
- **Critical paths**: 100% coverage (crypto, signing, transaction polling)
- **Public methods**: All must have tests
- **Edge cases**: Error handling, invalid input, network failures

---

## Common Tasks

### Adding a New API Method

1. **Add method to CircularProtocolAPI class:**

```php
/**
 * [Description]
 *
 * @param array $request Request parameters
 * @return array Response with Result and Response fields
 * @throws CircularProtocolException
 */
public function newMethod(array $request): array
{
    return $this->makeRequest('NewEndpoint', $request);
}
```

2. **Update README.md** with method documentation

3. **Add test cases:**
```php
// In CircularProtocolIntegrationTest.php
public function test_new_method(): void
{
    $result = $this->api->newMethod(['Version' => '1.0.9']);
    $this->assertEquals(200, $result['Result']);
}
```

4. **Update CHANGELOG.md:**
```markdown
### Added
- `newMethod()` - Description of what it does
```

### Modifying Existing Method

1. **Check backward compatibility** - Can you avoid breaking changes?
2. **Update docblock** if signature changes
3. **Update tests** to cover new behavior
4. **Update CHANGELOG.md** under `### Changed`
5. **Update README.md** if user-facing behavior changes

### Adding a Helper Method

**For public helpers:**
```php
/**
 * [Description]
 *
 * @param string $input Input description
 * @return string Output description
 */
public function helperName(string $input): string
{
    // Implementation
}
```

**For private helpers:**
```php
/**
 * [Internal description]
 */
private function helperName(string $input): string
{
    // Implementation
}
```

---

## Error Handling

### Exception Hierarchy

```
Exception (PHP built-in)
└── CircularProtocolException (SDK custom)
```

**Future Enhancement:** Add specialized exceptions (see Python SDK):
- `AuthenticationError`
- `InsufficientBalanceError`
- `RateLimitError`
- `TransactionFailedError`
- `ValidationError`

### Throwing Exceptions

```php
throw new CircularProtocolException(
    $errorMessage,    // User-friendly error message
    $statusCode,      // HTTP status or API result code
    $endpoint         // Endpoint that failed
);
```

### Handling Exceptions

```php
try {
    $result = $api->sendTransaction($request);
} catch (CircularProtocolException $e) {
    error_log('Transaction failed: ' . $e->getMessage());
    error_log('Endpoint: ' . $e->getEndpoint());
    error_log('Status: ' . $e->getStatusCode());
}
```

---

## Security Considerations

### Private Key Handling

**NEVER:**
- Log private keys
- Echo private keys in error messages
- Store private keys in version control
- Hardcode private keys

```php
// ❌ NEVER do this
$privateKey = '1234567890abcdef...';

// ✅ Always use secure storage
$privateKey = getenv('CIRCULAR_PRIVATE_KEY');
```

### Input Validation

**Always validate:**
- Address formats (64-char hex with optional 0x prefix)
- Hex strings
- Required fields presence

```php
// ✅ Good: Validate before use
if (!preg_match('/^(0x)?[a-fA-F0-9]{64}$/', $address)) {
    throw new CircularProtocolException('Invalid address format', 400, 'validateAddress');
}
```

### HTTPS Only

```php
// ❌ Never allow HTTP in production
if (str_starts_with($this->nagUrl, 'http://')) {
    throw new CircularProtocolException('HTTPS required for security', 0, 'setNagUrl');
}
```

---

## Release Process

### Version Numbering (SemVer)

- **MAJOR** (x.0.0): Breaking changes
- **MINOR** (1.x.0): New features, backward compatible
- **PATCH** (1.0.x): Bug fixes, backward compatible

### Release Checklist

1. **Update version** in:
   - `composer.json`
   - `src/CircularProtocolAPI.php` (class docblock)
   - `README.md`

2. **Update CHANGELOG.md:**
   - Move `[Unreleased]` to `[X.Y.Z] - YYYY-MM-DD`
   - Add new `[Unreleased]` section

3. **Run quality checks:**
   ```bash
   composer test
   composer cs:check
   composer phpstan
   ```

4. **Create git tag:**
   ```bash
   git tag -a v1.0.9 -m "Release version 1.0.9"
   git push origin v1.0.9
   ```

5. **Publish to Packagist** (auto-updates from GitHub releases)

---

## AI Assistant Guidelines

### When Reviewing Code

**Check for:**
- [ ] PSR-12 compliance (use `composer cs:check`)
- [ ] Type hints on all public methods
- [ ] PHPDoc comments with `@param`, `@return`, `@throws`
- [ ] No hardcoded secrets or keys
- [ ] Backward compatibility maintained
- [ ] Tests updated for changes
- [ ] CHANGELOG.md updated

### When Suggesting Changes

**Always:**
- Explain WHY, not just WHAT
- Show before/after examples
- Consider backward compatibility impact
- Suggest test cases
- Reference this document for patterns

**Never:**
- Remove existing functionality without discussion
- Change public APIs without major version bump
- Add dependencies without justification
- Skip writing tests

### When Adding Features

**Follow this flow:**
1. Understand the requirement
2. Check if similar pattern exists in codebase
3. Follow existing patterns (see [Code Patterns](#code-patterns))
4. Write tests FIRST (TDD approach)
5. Implement feature
6. Update documentation
7. Run quality checks

---

## Quick Reference

### File Locations

- Main API class: `src/CircularProtocolAPI.php`
- Tests: `tests/CircularProtocol*.php`
- Config: `composer.json`
- Docs: `README.md`, `CHANGELOG.md`, `CONTRIBUTING.md`

### Common Commands

```bash
# Run all tests
composer test

# Run specific test suite
composer test:unit
composer test:integration
composer test:e2e

# Code style check
composer cs:check
composer cs:fix

# Static analysis
composer phpstan

# Install dependencies
composer install

# Update dependencies
composer update
```

### Endpoint Naming Convention

API endpoint names follow this pattern:
- Format: `PascalCase` with `Get` prefix for reads
- Examples: `GetWallet`, `GetBlockCount`, `AddTransaction`
- Method names: `camelCase` PHP convention

### Method Naming Convention

- API methods: `camelCase` (e.g., `getWallet()`, `sendTransaction()`)
- Private helpers: `camelCase` (e.g., `hexFix()`, `padNumber()`)
- Constants: `UPPER_SNAKE_CASE`

---

## Version History

- **v1.0** - Initial AGENTS.md (January 15, 2025)

---

**For Questions:** See [CONTRIBUTING.md](CONTRIBUTING.md) or contact info@circularlabs.io
