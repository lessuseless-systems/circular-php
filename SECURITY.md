# Security Policy

## Supported Versions

The following versions of the Circular Protocol PHP SDK are currently supported with security updates:

| Version | Supported          |
| ------- | ------------------ |
| 1.0.9   | :white_check_mark: |
| 1.0.8   | :white_check_mark: |
| < 1.0.8 | :x:                |

## Reporting a Vulnerability

The Circular Protocol team takes security vulnerabilities seriously. We appreciate your efforts to responsibly disclose your findings.

### How to Report

**Please DO NOT report security vulnerabilities through public GitHub issues.**

Instead, please report security vulnerabilities via email to:

**Email:** [info@circularlabs.io](mailto:info@circularlabs.io)

**Subject Line:** `[SECURITY] Circular PHP SDK - [Brief Description]`

### What to Include

Please include the following information in your report:

1. **Description** - Detailed description of the vulnerability
2. **Impact** - Potential impact of the vulnerability
3. **Reproduction Steps** - Step-by-step instructions to reproduce the issue
4. **Affected Versions** - Which versions are affected
5. **Proof of Concept** - Code snippets or examples demonstrating the issue
6. **Suggested Fix** - If you have suggestions for remediation (optional)
7. **Your Details** - Your name/handle and contact information for follow-up

### Example Report

```
Subject: [SECURITY] Circular PHP SDK - Potential Signature Bypass

Description:
The verifySignature() method may be vulnerable to signature malleability
attacks due to improper DER encoding validation.

Impact:
Attackers could potentially forge signatures by manipulating the encoding
without invalidating the cryptographic signature.

Reproduction:
1. Create a valid signature using signMessage()
2. Modify the DER encoding structure
3. Call verifySignature() with modified signature
4. Signature is incorrectly validated as authentic

Affected Versions:
1.0.8, 1.0.9

Proof of Concept:
[Code snippet or detailed example]

Suggested Fix:
Add strict DER encoding validation before signature verification.

Reporter:
John Doe (john@example.com)
```

## Response Timeline

We will make our best effort to respond according to the following SLAs:

| Severity | Initial Response | Status Updates | Target Fix |
|----------|-----------------|----------------|------------|
| Critical | 24 hours        | Every 48 hours | 7 days     |
| High     | 48 hours        | Weekly         | 30 days    |
| Medium   | 5 business days | Bi-weekly      | 90 days    |
| Low      | 10 business days| Monthly        | Best effort|

### Severity Levels

**Critical:**
- Remote code execution
- Private key extraction
- Authentication bypass
- Signature forgery

**High:**
- Denial of service
- Information disclosure (API keys, sensitive data)
- Privilege escalation

**Medium:**
- Cross-site scripting (if web interface exists)
- Input validation issues
- Minor information leaks

**Low:**
- Theoretical vulnerabilities
- Low-impact issues requiring complex attack scenarios

## Disclosure Policy

### Coordinated Disclosure

We follow a **coordinated disclosure** policy:

1. **Report Received** - We acknowledge receipt within the SLA timeframe
2. **Investigation** - We investigate and validate the vulnerability
3. **Fix Development** - We develop and test a fix
4. **Coordinated Release** - We coordinate release timing with the reporter
5. **Public Disclosure** - Vulnerability details are disclosed after fix is released

### Disclosure Timeline

- **Day 0:** Vulnerability reported
- **Day 1-7:** Investigation and validation
- **Day 7-30:** Fix development and testing
- **Day 30:** Coordinated release (or earlier for critical issues)
- **Day 30+:** Public disclosure after release

We request that reporters:
- Allow us reasonable time to fix the vulnerability before public disclosure
- Avoid exploiting the vulnerability beyond proof-of-concept
- Do not access, modify, or delete data belonging to others

## Security Best Practices

### For SDK Users

When using the Circular Protocol PHP SDK, follow these security best practices:

#### 1. Protect Private Keys

```php
// ❌ NEVER hardcode private keys
$privateKey = '1234567890abcdef...';

// ✅ Use environment variables
$privateKey = getenv('CIRCULAR_PRIVATE_KEY');

// ✅ Use secure key management
$privateKey = file_get_contents('/secure/path/to/key');
```

#### 2. Validate Input

```php
// ✅ Validate addresses before use
if (!preg_match('/^0x[a-fA-F0-9]{64}$/', $address)) {
    throw new InvalidArgumentException('Invalid address format');
}
```

#### 3. Use HTTPS Only

```php
// ✅ Always use HTTPS endpoints
$api = new CircularProtocolAPI('https://nag.circularlabs.io/NAG.php?cep=');

// ❌ Never use HTTP for production
// $api = new CircularProtocolAPI('http://...');
```

#### 4. Handle Exceptions Properly

```php
try {
    $result = $api->sendTransaction($request);
} catch (CircularProtocolException $e) {
    // ✅ Log error securely (don't expose sensitive data)
    error_log('Transaction failed: ' . $e->getMessage());

    // ❌ Don't expose internal details to users
    // echo 'Error: ' . $e->getMessage();
}
```

#### 5. Keep Dependencies Updated

```bash
# Regularly update dependencies
composer update

# Check for security vulnerabilities
composer audit
```

#### 6. Secure Configuration

```php
// ✅ Store API keys securely
$nagKey = getenv('CIRCULAR_NAG_KEY');
$api->setNagKey($nagKey);

// ❌ Don't commit API keys to version control
// Add .env files to .gitignore
```

### For Contributors

If you're contributing to the SDK:

1. **Code Review** - All code must be reviewed before merging
2. **Input Validation** - Validate all external input
3. **No Secrets** - Never commit secrets, keys, or credentials
4. **Dependency Audits** - Run `composer audit` before releases
5. **Secure Defaults** - Use secure defaults in all configurations
6. **Error Messages** - Don't leak sensitive information in error messages

## Security Updates

Security updates are released as:

1. **Patch Releases** - For backward-compatible security fixes (e.g., 1.0.9 → 1.0.10)
2. **Security Advisories** - Published on GitHub Security Advisories
3. **CHANGELOG** - Documented in CHANGELOG.md with `[SECURITY]` tag

## Bug Bounty Program

Currently, we do not have a formal bug bounty program. However, we deeply appreciate security research and will publicly acknowledge researchers who report valid vulnerabilities (with their permission).

## Hall of Fame

We will maintain a list of security researchers who have responsibly disclosed vulnerabilities:

<!-- Security researchers will be listed here -->

*No vulnerabilities have been reported yet.*

## Contact

For security-related questions or concerns:

- **Email:** [info@circularlabs.io](mailto:info@circularlabs.io)
- **Subject:** `[SECURITY] Circular PHP SDK`

For non-security issues, please use [GitHub Issues](https://github.com/circular-protocol/circular-php/issues).

---

**Last Updated:** January 15, 2025
**Version:** 1.0
