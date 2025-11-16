# Contributing to Circular Protocol PHP SDK

Thank you for your interest in contributing to the Circular Protocol PHP SDK! This document provides guidelines and instructions for contributing.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [Coding Standards](#coding-standards)
- [Testing](#testing)
- [Pull Request Process](#pull-request-process)
- [Release Process](#release-process)

---

## Code of Conduct

This project adheres to the [Contributor Covenant Code of Conduct](CODE_OF_CONDUCT.md). By participating, you are expected to uphold this code. Please report unacceptable behavior to [info@circularlabs.io](mailto:info@circularlabs.io).

---

## Getting Started

### Prerequisites

- **PHP 8.0 or higher** with Composer
- **Git** for version control
- **phpseclib3** for cryptographic operations (installed via Composer)

### Fork and Clone

1. Fork the repository on GitHub
2. Clone your fork locally:
   ```bash
   git clone https://github.com/YOUR_USERNAME/circular-php.git
   cd circular-php
   ```

3. Add upstream remote:
   ```bash
   git remote add upstream https://github.com/circular-protocol/circular-php.git
   ```

---

## Development Setup

### Install Dependencies

```bash
composer install
```

### Verify Installation

```bash
composer test:unit
```

---

## Coding Standards

### PHP Standards

This project follows **PSR-12** coding standards with the following conventions:

#### Naming Conventions

- **Classes**: `PascalCase` (e.g., `CircularProtocolAPI`)
- **Methods**: `camelCase` (e.g., `getWallet()`, `sendTransaction()`)
- **Constants**: `UPPER_SNAKE_CASE` (e.g., `API_VERSION`)
- **Properties**: `camelCase` (e.g., `$nagUrl`, `$apiKey`)
- **Private methods**: `camelCase` with no prefix (e.g., `makeRequest()`)

#### Type Hints

All public methods must include type hints:

```php
public function sendTransaction(array $request): array
{
    // Implementation
}
```

#### PHPDoc Comments

All public methods require comprehensive PHPDoc:

```php
/**
 * Submit transaction to blockchain
 *
 * Submits a transaction to the blockchain. Requires a complete signed transaction
 * including ID, addresses, payload, nonce, and signature.
 *
 * @param array $request Request parameters containing transaction data
 * @return array Response with Result and Response fields
 * @throws CircularProtocolException If request fails or returns error
 *
 * @example
 * ```php
 * $result = $api->sendTransaction([
 *     'ID' => '0x...',
 *     'From' => '0x...',
 *     'To' => '0x...',
 *     'Payload' => '0x...',
 *     'Nonce' => '1',
 *     'Signature' => '0x...',
 *     'Timestamp' => '2025:01:15-12:30:45',
 *     'Type' => 'C_TYPE_TRANSACTION',
 *     'Blockchain' => 'MainNet',
 *     'Version' => '1.0.9'
 * ]);
 * ```
 */
public function sendTransaction(array $request): array
```

### File Structure

```
circular-php/
├── src/
│   └── CircularProtocolAPI.php    # Main API class
├── tests/
│   ├── CircularProtocolUnitTest.php
│   ├── CircularProtocolIntegrationTest.php
│   └── CircularProtocolE2ETest.php
├── composer.json
├── README.md
├── CHANGELOG.md
├── CONTRIBUTING.md
├── CODE_OF_CONDUCT.md
├── SECURITY.md
└── LICENSE
```

---

## Testing

### Test Types

1. **Unit Tests** - Test individual methods with mocked dependencies
2. **Integration Tests** - Test against mock API server
3. **E2E Tests** - Test against real NAG endpoints (requires credentials)

### Running Tests

```bash
# Run all tests
composer test

# Run unit tests only
composer test:unit

# Run integration tests (requires mock server)
composer test:integration

# Run E2E tests (requires environment variables)
CIRCULAR_TEST_ADDRESS=0x... composer test:e2e
```

### Writing Tests

Tests use PHPUnit. Example test structure:

```php
/**
 * @test
 * @group unit
 */
public function testMethodName(): void
{
    // Arrange
    $api = new CircularProtocolAPI();

    // Act
    $result = $api->someMethod(['param' => 'value']);

    // Assert
    $this->assertEquals(200, $result['Result']);
    $this->assertNotNull($result['Response']);
}
```

### Test Coverage Requirements

- Minimum **80% code coverage** for new features
- All public methods must have tests
- Edge cases and error handling must be tested

---

## Pull Request Process

### Before Submitting

1. **Create a feature branch** from `main`:
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Make your changes** following coding standards

3. **Add/update tests** for your changes

4. **Run all tests** to ensure they pass:
   ```bash
   composer test
   ```

5. **Update documentation**:
   - Add entry to `CHANGELOG.md` under `[Unreleased]`
   - Update `README.md` if adding new features
   - Add PHPDoc comments to new methods

6. **Commit with clear messages**:
   ```bash
   git commit -m "feat: add new feature X"
   git commit -m "fix: resolve issue with Y"
   git commit -m "docs: update README for Z"
   ```

### Commit Message Format

Follow [Conventional Commits](https://www.conventionalcommits.org/):

- `feat:` - New feature
- `fix:` - Bug fix
- `docs:` - Documentation changes
- `test:` - Test additions/changes
- `refactor:` - Code refactoring
- `chore:` - Maintenance tasks
- `style:` - Code style changes (formatting, etc.)

### Submitting Pull Request

1. **Push to your fork**:
   ```bash
   git push origin feature/your-feature-name
   ```

2. **Create Pull Request** on GitHub with:
   - Clear title describing the change
   - Description explaining what and why
   - Reference to related issues (if any)
   - Test results
   - Screenshots (if applicable)

3. **PR Checklist**:
   - [ ] Code follows PSR-12 standards
   - [ ] All tests pass
   - [ ] New tests added for new features
   - [ ] PHPDoc comments added/updated
   - [ ] CHANGELOG.md updated
   - [ ] No breaking changes (or clearly documented)
   - [ ] Backward compatibility maintained

### Review Process

1. Maintainers will review your PR
2. Address any feedback or requested changes
3. Once approved, maintainers will merge

---

## Release Process

Releases follow [Semantic Versioning](https://semver.org/):

- **MAJOR** version for incompatible API changes
- **MINOR** version for backward-compatible functionality
- **PATCH** version for backward-compatible bug fixes

### Version Bumping

1. Update version in:
   - `composer.json`
   - `src/CircularProtocolAPI.php` (docblock)
   - `README.md`

2. Update `CHANGELOG.md`:
   - Move `[Unreleased]` changes to new version section
   - Add release date
   - Create new `[Unreleased]` section

3. Create git tag:
   ```bash
   git tag -a v1.0.9 -m "Release version 1.0.9"
   git push origin v1.0.9
   ```

---

## Getting Help

- **Documentation**: [GitBook](https://circular-protocol.gitbook.io/circular-sdk/api-docs/php)
- **Issues**: [GitHub Issues](https://github.com/circular-protocol/circular-php/issues)
- **Email**: info@circularlabs.io

---

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

---

Thank you for contributing to the Circular Protocol PHP SDK! 🚀
