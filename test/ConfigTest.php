<?php declare(strict_types=1);

namespace EmsApi\Test;

use EmsApi\Config;
use Exception;

class ConfigTest extends Base
{
    /**
     * @return void
     * @throws \ReflectionException
     */
    final public function testConstructorWithEmptyArray()
    {
        $config = new Config();
        
        $this->assertInstanceOf(Config::class, $config);
        $this->assertEquals('utf-8', $config->charset);
    }

    /**
     * @return void
     * @throws \ReflectionException
     */
    final public function testConstructorWithConfig()
    {
        $config = new Config([
            'apiUrl' => 'https://example.com/api',
            'apiKey' => 'test-api-key-123',
            'charset' => 'iso-8859-1'
        ]);
        
        $this->assertEquals('iso-8859-1', $config->charset);
        $this->assertEquals('test-api-key-123', $config->getApiKey());
        $this->assertEquals('https://example.com/api/', $config->getApiUrl());
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testSetApiUrlValid()
    {
        $config = new Config();
        
        $result = $config->setApiUrl('https://example.com/api');
        
        $this->assertInstanceOf(Config::class, $result);
        $this->assertEquals('https://example.com/api/', $config->getApiUrl());
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testSetApiUrlWithTrailingSlash()
    {
        $config = new Config();
        
        $config->setApiUrl('https://example.com/api/');
        
        // Should normalize to single trailing slash
        $this->assertEquals('https://example.com/api/', $config->getApiUrl());
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testSetApiUrlWithMultipleTrailingSlashes()
    {
        $config = new Config();
        
        $config->setApiUrl('https://example.com/api///');
        
        // Should normalize to single trailing slash
        $this->assertEquals('https://example.com/api/', $config->getApiUrl());
    }

    /**
     * @return void
     */
    final public function testSetApiUrlInvalid()
    {
        $config = new Config();
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Please set a valid api base url.');
        $config->setApiUrl('not-a-valid-url');
    }

    /**
     * @return void
     */
    final public function testSetApiUrlEmpty()
    {
        $config = new Config();
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Please set a valid api base url.');
        $config->setApiUrl('');
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testGetApiUrlWithoutSetting()
    {
        $config = new Config();
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Please set the api base url.');
        $config->getApiUrl();
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testGetApiUrlWithEndpoint()
    {
        $config = new Config();
        $config->setApiUrl('https://example.com/api');
        
        $url = $config->getApiUrl('lists');
        
        $this->assertEquals('https://example.com/api/lists', $url);
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testGetApiUrlWithNestedEndpoint()
    {
        $config = new Config();
        $config->setApiUrl('https://example.com/api');
        
        $url = $config->getApiUrl('lists/abc123/subscribers');
        
        $this->assertEquals('https://example.com/api/lists/abc123/subscribers', $url);
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testSetApiKey()
    {
        $config = new Config();
        
        $result = $config->setApiKey('my-secret-key');
        
        $this->assertInstanceOf(Config::class, $result);
        $this->assertEquals('my-secret-key', $config->getApiKey());
    }

    /**
     * @return void
     */
    final public function testGetApiKeyWithoutSetting()
    {
        $config = new Config();
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Please set the api key.');
        $config->getApiKey();
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testGetApiKeyEmpty()
    {
        $config = new Config();
        $config->setApiKey('');
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Please set the api key.');
        $config->getApiKey();
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testCharsetProperty()
    {
        $config = new Config();
        
        // Default charset
        $this->assertEquals('utf-8', $config->charset);
        
        // Change charset
        $config->charset = 'iso-8859-1';
        $this->assertEquals('iso-8859-1', $config->charset);
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testCharsetViaConstructor()
    {
        $config = new Config(['charset' => 'utf-16']);
        
        $this->assertEquals('utf-16', $config->charset);
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testFluentInterface()
    {
        $config = new Config();
        
        $result = $config
            ->setApiUrl('https://example.com/api')
            ->setApiKey('test-key');
        
        $this->assertInstanceOf(Config::class, $result);
        $this->assertEquals('https://example.com/api/', $config->getApiUrl());
        $this->assertEquals('test-key', $config->getApiKey());
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testMultipleProtocols()
    {
        $config = new Config();
        
        // Test HTTP
        $config->setApiUrl('http://example.com/api');
        $this->assertEquals('http://example.com/api/', $config->getApiUrl());
        
        // Test HTTPS
        $config->setApiUrl('https://secure.example.com/api');
        $this->assertEquals('https://secure.example.com/api/', $config->getApiUrl());
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testApiUrlWithPort()
    {
        $config = new Config();
        
        $config->setApiUrl('https://example.com:8080/api');
        $this->assertEquals('https://example.com:8080/api/', $config->getApiUrl());
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function testApiUrlWithQueryString()
    {
        $config = new Config();
        
        // Note: query strings should not be in the base URL, but testing behavior
        $config->setApiUrl('https://example.com/api?version=1');
        $this->assertEquals('https://example.com/api?version=1/', $config->getApiUrl());
    }
}
