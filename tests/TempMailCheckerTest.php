<?php

namespace TempMailChecker\Tests;

use PHPUnit\Framework\TestCase;
use TempMailChecker\TempMailChecker;
use Exception;

class TempMailCheckerTest extends TestCase
{
    private $apiKey;
    
    protected function setUp(): void
    {
        // Use a test API key from environment or skip tests
        $this->apiKey = getenv('TEMPMAILCHECKER_API_KEY') ?: 'test_key';
        
        if ($this->apiKey === 'test_key') {
            $this->markTestSkipped('TEMPMAILCHECKER_API_KEY not set');
        }
    }
    
    public function testCanInstantiate()
    {
        $checker = new TempMailChecker($this->apiKey);
        $this->assertInstanceOf(TempMailChecker::class, $checker);
    }
    
    public function testRequiresApiKey()
    {
        $this->expectException(Exception::class);
        new TempMailChecker('');
    }
    
    public function testCheckValidEmail()
    {
        $checker = new TempMailChecker($this->apiKey);
        $result = $checker->check('test@gmail.com');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('temp', $result);
        $this->assertIsBool($result['temp']);
    }
    
    public function testIsDisposableMethod()
    {
        $checker = new TempMailChecker($this->apiKey);
        $isDisposable = $checker->isDisposable('test@10minutemail.com');
        
        $this->assertIsBool($isDisposable);
    }
    
    public function testCheckDomain()
    {
        $checker = new TempMailChecker($this->apiKey);
        $result = $checker->checkDomain('tempmail.com');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('temp', $result);
    }
    
    public function testInvalidEmailFormat()
    {
        $checker = new TempMailChecker($this->apiKey);
        
        $this->expectException(Exception::class);
        $checker->check('not-an-email');
    }
    
    public function testEmptyEmail()
    {
        $checker = new TempMailChecker($this->apiKey);
        
        $this->expectException(Exception::class);
        $checker->check('');
    }
    
    public function testSetTimeout()
    {
        $checker = new TempMailChecker($this->apiKey);
        $result = $checker->setTimeout(15);
        
        $this->assertInstanceOf(TempMailChecker::class, $result);
    }
}

