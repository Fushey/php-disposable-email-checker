<?php

require __DIR__ . '/../vendor/autoload.php';

use TempMailChecker\TempMailChecker;
use Exception;

// Replace with your actual API key
$apiKey = 'your_api_key_here';

// Initialize the checker (defaults to EU endpoint)
$checker = new TempMailChecker($apiKey);

// Or use a specific regional endpoint:
// $checker = new TempMailChecker($apiKey, TempMailChecker::ENDPOINT_US);
// $checker = new TempMailChecker($apiKey, TempMailChecker::ENDPOINT_ASIA);

// Example 1: Check if an email is disposable
echo "Example 1: Check email address\n";
echo "================================\n";

$emails = [
    'user@gmail.com',
    'test@10minutemail.com',
    'spam@guerrillamail.com',
];

foreach ($emails as $email) {
    try {
        $isDisposable = $checker->isDisposable($email);
        $status = $isDisposable ? '❌ Disposable' : '✅ Legitimate';
        echo sprintf("%-30s %s\n", $email, $status);
    } catch (Exception $e) {
        echo sprintf("%-30s Error: %s\n", $email, $e->getMessage());
    }
}

echo "\n";

// Example 2: Get full response
echo "Example 2: Get full response\n";
echo "============================\n";

try {
    $result = $checker->check('user@tempmail.com');
    echo "Response: " . json_encode($result, JSON_PRETTY_PRINT) . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 3: Check domain only
echo "Example 3: Check domain\n";
echo "=======================\n";

$domains = ['gmail.com', 'tempmail.com', 'mailinator.com'];

foreach ($domains as $domain) {
    try {
        $isDisposable = $checker->isDisposableDomain($domain);
        $status = $isDisposable ? '❌ Disposable' : '✅ Legitimate';
        echo sprintf("%-30s %s\n", $domain, $status);
    } catch (Exception $e) {
        echo sprintf("%-30s Error: %s\n", $domain, $e->getMessage());
    }
}

echo "\n";

// Example 4: Check usage
echo "Example 4: Check API usage\n";
echo "=========================\n";

try {
    $usage = $checker->getUsage();
    echo sprintf("Used today: %d / %d\n", $usage['usage_today'], $usage['limit']);
    echo "Resets: " . $usage['reset'] . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

