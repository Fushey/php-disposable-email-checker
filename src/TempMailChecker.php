<?php

namespace TempMailChecker;

use Exception;

/**
 * TempMailChecker PHP SDK
 * 
 * Detect disposable email addresses using the TempMailChecker API.
 * 
 * @package TempMailChecker
 * @version 1.0.0
 */
class TempMailChecker
{
    /**
     * Regional endpoint URLs
     * All endpoints use /check and /usage directly (no /api prefix)
     */
    public const ENDPOINT_EU = 'https://tempmailchecker.com';
    public const ENDPOINT_US = 'https://us.tempmailchecker.com';
    public const ENDPOINT_ASIA = 'https://asia.tempmailchecker.com';
    
    /**
     * Default base API URL (EU endpoint)
     */
    private const BASE_URL = self::ENDPOINT_EU;
    
    /**
     * API key for authentication
     * 
     * @var string
     */
    private $apiKey;
    
    /**
     * Custom endpoint URL (optional, for regional endpoints)
     * 
     * @var string|null
     */
    private $endpoint;
    
    /**
     * cURL timeout in seconds
     * 
     * @var int
     */
    private $timeout = 10;
    
    /**
     * Create a new TempMailChecker instance
     * 
     * @param string $apiKey Your TempMailChecker API key
     * @param string|null $endpoint Optional custom endpoint. Use constants:
     *                              - TempMailChecker::ENDPOINT_EU (default)
     *                              - TempMailChecker::ENDPOINT_US
     *                              - TempMailChecker::ENDPOINT_ASIA
     *                              Or provide full base URL: 'https://us.tempmailchecker.com'
     *                              Note: All endpoints use /check and /usage directly (no /api prefix)
     */
    public function __construct(string $apiKey, ?string $endpoint = null)
    {
        if (empty($apiKey)) {
            throw new Exception('API key is required');
        }
        
        $this->apiKey = $apiKey;
        $this->endpoint = $endpoint;
    }
    
    /**
     * Set request timeout
     * 
     * @param int $seconds Timeout in seconds
     * @return self
     */
    public function setTimeout(int $seconds): self
    {
        $this->timeout = $seconds;
        return $this;
    }
    
    /**
     * Check if an email address is disposable
     * 
     * @param string $email Full email address to check
     * @return bool True if disposable, false if legitimate
     * @throws Exception On API errors
     */
    public function isDisposable(string $email): bool
    {
        $result = $this->check($email);
        return $result['temp'] === true;
    }
    
    /**
     * Check if a domain is disposable
     * 
     * @param string $domain Domain name to check (e.g., 'tempmail.com')
     * @return bool True if disposable, false if legitimate
     * @throws Exception On API errors
     */
    public function isDisposableDomain(string $domain): bool
    {
        $result = $this->checkDomain($domain);
        return $result['temp'] === true;
    }
    
    /**
     * Check an email address and return full response
     * 
     * @param string $email Full email address to check
     * @return array Response array with 'temp' boolean
     * @throws Exception On API errors
     */
    public function check(string $email): array
    {
        if (empty($email)) {
            throw new Exception('Email address is required');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email address format');
        }
        
        $url = $this->getApiUrl('/check');
        $params = ['email' => $email];
        
        return $this->makeRequest($url, $params);
    }
    
    /**
     * Check a domain and return full response
     * 
     * @param string $domain Domain name to check
     * @return array Response array with 'temp' boolean
     * @throws Exception On API errors
     */
    public function checkDomain(string $domain): array
    {
        if (empty($domain)) {
            throw new Exception('Domain is required');
        }
        
        // Remove protocol if present
        $domain = preg_replace('#^https?://#', '', $domain);
        // Remove path if present
        $domain = explode('/', $domain)[0];
        // Remove port if present
        $domain = explode(':', $domain)[0];
        
        $url = $this->getApiUrl('/check');
        $params = ['domain' => $domain];
        
        return $this->makeRequest($url, $params);
    }
    
    /**
     * Get current API usage statistics
     * 
     * @return array Usage stats with 'usage_today', 'limit', 'reset'
     * @throws Exception On API errors
     */
    public function getUsage(): array
    {
        $url = $this->getApiUrl('/usage');
        $params = ['key' => $this->apiKey];
        
        return $this->makeRequest($url, $params, false);
    }
    
    /**
     * Get the full API URL
     * 
     * All endpoints use paths directly: /check, /usage (no /api prefix)
     * 
     * @param string $path API endpoint path (e.g., '/check', '/usage')
     * @return string Full URL
     */
    private function getApiUrl(string $path): string
    {
        $base = $this->endpoint ?? self::BASE_URL;
        return rtrim($base, '/') . $path;
    }
    
    /**
     * Make an API request
     * 
     * @param string $url Full API URL
     * @param array $params Query parameters
     * @param bool $requireAuth Whether to include API key header
     * @return array Decoded JSON response
     * @throws Exception On API errors
     */
    private function makeRequest(string $url, array $params = [], bool $requireAuth = true): array
    {
        $queryString = http_build_query($params);
        $fullUrl = $url . ($queryString ? '?' . $queryString : '');
        
        $ch = curl_init($fullUrl);
        
        $headers = [];
        if ($requireAuth) {
            $headers[] = 'X-API-Key: ' . $this->apiKey;
        }
        
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_FOLLOWLOCATION => true,
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception('cURL error: ' . $error);
        }
        
        if ($httpCode === 429) {
            $data = json_decode($response, true);
            throw new Exception('Rate limit exceeded: ' . ($data['message'] ?? 'Daily limit reached'));
        }
        
        if ($httpCode !== 200) {
            $data = json_decode($response, true);
            $errorMsg = $data['error'] ?? 'API request failed';
            throw new Exception($errorMsg . ' (HTTP ' . $httpCode . ')');
        }
        
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON response: ' . json_last_error_msg());
        }
        
        return $data;
    }
}

