<div align="center">
  <img src="header.png" alt="TempMailChecker PHP SDK" width="100%">
</div>

# TempMailChecker PHP SDK

[![Latest Release](https://img.shields.io/github/v/release/Fushey/php-disposable-email-checker?style=flat-square&logo=github&cacheSeconds=300)](https://github.com/Fushey/php-disposable-email-checker/releases/latest)
[![License](https://img.shields.io/github/license/Fushey/php-disposable-email-checker?style=flat-square)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-777BB4?style=flat-square&logo=php)](https://php.net)
[![GitHub stars](https://img.shields.io/github/stars/Fushey/php-disposable-email-checker?style=flat-square)](https://github.com/Fushey/php-disposable-email-checker/stargazers)

> **Detect disposable email addresses in real-time** with the TempMailChecker API. Block fake signups, prevent spam, and protect your platform from abuse.

## 🚀 Quick Start

### Installation

```bash
composer require tempmailchecker/php-sdk
```

### Basic Usage

```php
<?php

require 'vendor/autoload.php';

use TempMailChecker\TempMailChecker;

// Initialize with your API key
$checker = new TempMailChecker('your_api_key_here');

// Check if an email is disposable
if ($checker->isDisposable('user@tempmail.com')) {
    echo "Blocked: This is a disposable email";
} else {
    echo "Valid: This is a legitimate email";
}
```

## 📖 Documentation

### Check Email Address

```php
$checker = new TempMailChecker('your_api_key');

// Simple boolean check
$isDisposable = $checker->isDisposable('test@10minutemail.com');

// Get full response
$result = $checker->check('user@example.com');
// Returns: ['temp' => false]
```

### Check Domain

```php
// Check just the domain
$isDisposable = $checker->isDisposableDomain('tempmail.com');

// Get full response
$result = $checker->checkDomain('guerrillamail.com');
```

### Regional Endpoints

Use regional endpoints for lower latency:

```php
// US endpoint
$checker = new TempMailChecker(
    'your_api_key',
    'https://us.tempmailchecker.com/api'
);

// Asia endpoint
$checker = new TempMailChecker(
    'your_api_key',
    'https://asia.tempmailchecker.com/api'
);
```

### Check Usage

```php
$usage = $checker->getUsage();
// Returns: ['usage_today' => 15, 'limit' => 25, 'reset' => 'midnight UTC']
```

### Error Handling

```php
try {
    $isDisposable = $checker->isDisposable('user@example.com');
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Rate limit') !== false) {
        // Handle rate limit
    } else {
        // Handle other errors
        echo "Error: " . $e->getMessage();
    }
}
```

## 🎯 Use Cases

- **Block Fake Signups**: Stop disposable emails at registration
- **Prevent Promo Abuse**: Protect referral programs and coupons
- **Clean Email Lists**: Remove throwaway addresses from newsletters
- **Reduce Spam**: Filter out disposable emails in contact forms
- **Protect Communities**: Ensure real users in forums and chat

## ⚡ Features

- ✅ **Simple API**: One method call, one boolean response
- ✅ **Fast**: Sub-millisecond processing, ~70ms real-world latency
- ✅ **Massive Database**: 277,000+ disposable email domains
- ✅ **Auto-Updates**: Database updated daily automatically
- ✅ **Regional Endpoints**: US, EU, and Asia for optimal performance
- ✅ **Free Tier**: 25 requests/day, no credit card required

## 🔑 Get Your API Key

1. Sign up at [tempmailchecker.com](https://tempmailchecker.com/signup)
2. Get 25 free requests per day
3. Start blocking disposable emails immediately

## 📚 Examples

### Laravel Integration

```php
use TempMailChecker\TempMailChecker;

class EmailValidationService
{
    private $checker;
    
    public function __construct()
    {
        $this->checker = new TempMailChecker(config('services.tempmailchecker.key'));
    }
    
    public function validateEmail(string $email): bool
    {
        return !$this->checker->isDisposable($email);
    }
}
```

### Symfony Integration

```php
// services.yaml
services:
    TempMailChecker\TempMailChecker:
        arguments:
            $apiKey: '%env(TEMPMAILCHECKER_API_KEY)%'
```

### WordPress Plugin

```php
add_filter('registration_errors', function($errors, $sanitized_user_login, $user_email) {
    $checker = new TempMailChecker(get_option('tempmailchecker_api_key'));
    
    if ($checker->isDisposable($user_email)) {
        $errors->add('disposable_email', 'Disposable email addresses are not allowed.');
    }
    
    return $errors;
}, 10, 3);
```

## 🛠️ Requirements

- PHP 7.4 or higher
- cURL extension
- JSON extension
- Composer (for installation)

## 📝 License

This library is open-source software licensed under the [MIT License](LICENSE).

## 🤝 Support

- **Documentation**: [tempmailchecker.com/docs](https://tempmailchecker.com/docs)
- **Issues**: [GitHub Issues](https://github.com/Fushey/php-disposable-email-checker/issues)
- **Email**: support@tempmailchecker.com

## ⭐ Why TempMailChecker?

- **277,000+ domains** in our database
- **Sub-millisecond** API processing
- **~70ms latency** from global endpoints
- **Auto-updates** daily
- **Free tier** with 25 requests/day
- **No SDK dependencies** - works with any PHP version

---

Made with ❤️ by [TempMailChecker](https://tempmailchecker.com)

