# Testing Checklist - Before PR Submission

## Prerequisites

Ensure you have PHP 8.0+ and Composer installed:

```bash
php -v  # Should show PHP 8.0 or higher
composer -V  # Should show Composer 2.x
```

---

## Step 1: Install Dependencies

```bash
cd /home/lessuseless/Projects/Orgs/Circular-Protocol/circular-php

# Install dependencies
composer install

# Verify installation
ls vendor/  # Should show installed packages
```

**Expected Output:**
- `autoload.php` created in `vendor/`
- `phpseclib/`, `phpunit/`, `phpstan/` directories in `vendor/`

---

## Step 2: Run All Tests

### 2a. Run Complete Test Suite

```bash
composer test
```

**Expected Output:**
```
PHPUnit 10.x by Sebastian Bergmann and contributors.

..........................                                 26 / 26 (100%)

Time: XX.XX seconds, Memory: XX.XX MB

OK (26 tests, XX assertions)
```

### 2b. Run Individual Test Suites

```bash
# Unit tests only
composer test:unit

# Integration tests (requires mock server)
composer test:integration

# E2E tests (requires environment variables)
CIRCULAR_TEST_ADDRESS=0x... composer test:e2e
```

**Common Test Failures to Watch For:**

1. **Missing phpseclib3 imports** - Should be fixed now ✅
   ```
   Error: Class 'EC' not found
   Error: Class 'BigInteger' not found
   ```

2. **Endpoint naming issues** - Should be fixed now ✅
   ```
   API Error: Endpoint not found (GetDomain)
   API Error: Endpoint not found (GetBlockCount)
   ```

3. **Method not found** - Check if tests use old method names
   ```
   Error: Call to undefined method addTransaction()
   Error: Call to undefined method getTransactionbyID()
   ```

---

## Step 3: Code Standards Check

```bash
composer cs:check
```

**Expected Output:**
```
PHP_CodeSniffer 3.x by Squiz (http://www.squiz.com.au)

Checking src/CircularProtocolAPI.php
..... 100 / 100 (100%)


Time: XXms; Memory: XX.XX MB
```

**If Issues Found:**

```bash
# Auto-fix code style issues
composer cs:fix

# Re-run check
composer cs:check
```

---

## Step 4: Static Analysis

```bash
composer phpstan
```

**Expected Output:**
```
PHPStan - PHP Static Analysis Tool 1.x

 [OK] No errors
```

**Common Issues:**

1. **Missing type hints** - Should be resolved
2. **Undefined properties** - Check class property declarations
3. **Invalid return types** - Verify method signatures

---

## Step 5: Manual Endpoint Verification (Optional)

Create a test script to verify endpoint changes work:

**File:** `test_endpoints.php`

```php
<?php

require 'vendor/autoload.php';

use Circular\Protocol\CircularProtocolAPI;

$api = new CircularProtocolAPI('https://nag.circularlabs.io/NAG.php?cep=');

// Test 1: getDomain (now uses ResolveDomain endpoint)
try {
    echo "Testing getDomain()...\n";
    $result = $api->getDomain([
        'Domain' => 'test.circular',
        'Blockchain' => 'MainNet',
        'Version' => '1.0.9'
    ]);
    echo "✅ getDomain works! Result: " . $result['Result'] . "\n";
} catch (Exception $e) {
    echo "⚠️  getDomain: " . $e->getMessage() . "\n";
}

// Test 2: getBlockCount (now uses GetBlockHeight endpoint)
try {
    echo "\nTesting getBlockCount()...\n";
    $result = $api->getBlockCount([
        'Blockchain' => 'MainNet',
        'Version' => '1.0.9'
    ]);
    echo "✅ getBlockCount works! Result: " . $result['Result'] . "\n";
    echo "   Block height: " . $result['Response'] . "\n";
} catch (Exception $e) {
    echo "⚠️  getBlockCount: " . $e->getMessage() . "\n";
}

// Test 3: Verify imports work (signMessage)
try {
    echo "\nTesting signMessage() (verifies phpseclib3 imports)...\n";
    $privateKey = str_repeat('0', 64); // Dummy key for testing
    $signature = $api->signMessage('test message', $privateKey);
    echo "✅ signMessage works! Signature length: " . strlen($signature) . "\n";
} catch (Exception $e) {
    echo "❌ signMessage FAILED: " . $e->getMessage() . "\n";
    echo "   This means phpseclib3 imports are missing!\n";
}

echo "\n=== All endpoint tests complete ===\n";
```

**Run:**
```bash
php test_endpoints.php
```

---

## Step 6: Review Test Coverage (Optional)

```bash
composer test:coverage

# Open coverage report
open coverage/index.html  # macOS
xdg-open coverage/index.html  # Linux
```

**Target Coverage:**
- Overall: 80%+
- Critical paths (crypto, signing): 100%

---

## Expected Results Summary

✅ **All tests should pass:**
- Unit tests: PASS
- Integration tests: PASS (if mock server available)
- Code standards: PASS
- PHPStan: 0 errors

✅ **Key fixes verified:**
- phpseclib3 imports working
- ResolveDomain endpoint working
- GetBlockHeight endpoint working
- All method names in camelCase

---

## If Tests Fail

### 1. Import Errors

**Error:** `Class 'phpseclib3\Crypt\EC' not found`

**Fix:**
```bash
# Ensure phpseclib is installed
composer require phpseclib/phpseclib:^3.0

# Verify imports in src/CircularProtocolAPI.php (lines 6-7):
# use phpseclib3\Crypt\EC;
# use phpseclib3\Math\BigInteger;
```

### 2. Endpoint Errors

**Error:** `API Error: Endpoint not found`

**Fix:** Check that endpoint names match circular-js-npm:
- `getDomain()` → `ResolveDomain` ✅
- `getBlockCount()` → `GetBlockHeight` ✅

### 3. Method Not Found

**Error:** `Call to undefined method`

**Fix:** Update test files to use new method names:
- `addTransaction()` → `sendTransaction()`
- `getTransactionbyID()` → `getTransactionById()`
- etc.

---

## Final Checklist Before PR

- [ ] `composer install` completed successfully
- [ ] `composer test` - All tests PASS
- [ ] `composer cs:check` - No style violations
- [ ] `composer phpstan` - No errors
- [ ] Manual endpoint test script works (optional)
- [ ] Git status is clean (all changes committed)
- [ ] Version 1.0.9 in all files
- [ ] CHANGELOG.md updated
- [ ] Temporary analysis files deleted:
  - [ ] Delete `PR_PREPARATION_ANALYSIS.md`
  - [ ] Delete `PR_SUMMARY.md`
  - [ ] Delete `TESTING_CHECKLIST.md` (this file)

---

## Quick Command Reference

```bash
# Install
composer install

# Test
composer test              # All tests
composer test:unit         # Unit only
composer test:integration  # Integration only
composer test:e2e          # E2E only

# Quality
composer cs:check          # Check code style
composer cs:fix            # Fix code style
composer phpstan           # Static analysis
composer test:coverage     # Coverage report

# Before PR
git status                 # Check changes
git add .                  # Stage all
git commit -m "..."        # Commit
git push                   # Push
```

---

**Once all tests pass, you're ready to submit the PR! 🚀**
