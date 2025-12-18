<?php

/**
 * Laravel Integration Example
 * 
 * Add this to your Laravel service provider or create a dedicated service class
 */

namespace App\Services;

use TempMailChecker\TempMailChecker;
use Exception;

class EmailValidationService
{
    private $checker;
    
    public function __construct()
    {
        $apiKey = config('services.tempmailchecker.key');
        
        if (empty($apiKey)) {
            throw new Exception('TempMailChecker API key not configured');
        }
        
        $this->checker = new TempMailChecker($apiKey);
    }
    
    /**
     * Validate email address (returns true if valid, false if disposable)
     */
    public function validate(string $email): bool
    {
        try {
            return !$this->checker->isDisposable($email);
        } catch (Exception $e) {
            // Log error and allow email (fail open)
            \Log::warning('TempMailChecker API error: ' . $e->getMessage());
            return true;
        }
    }
    
    /**
     * Check if email is disposable
     */
    public function isDisposable(string $email): bool
    {
        try {
            return $this->checker->isDisposable($email);
        } catch (Exception $e) {
            \Log::error('TempMailChecker error: ' . $e->getMessage());
            return false; // Fail closed - assume not disposable on error
        }
    }
}

/**
 * Usage in Laravel:
 * 
 * // In config/services.php
 * 'tempmailchecker' => [
 *     'key' => env('TEMPMAILCHECKER_API_KEY'),
 * ],
 * 
 * // In your controller or validation
 * $validator = app(EmailValidationService::class);
 * 
 * if (!$validator->validate($email)) {
 *     return back()->withErrors(['email' => 'Disposable email addresses are not allowed.']);
 * }
 */

