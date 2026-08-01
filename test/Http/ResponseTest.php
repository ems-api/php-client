<?php declare(strict_types=1);

namespace EmsApi\Test\Http;

use EmsApi\Http\Request;
use EmsApi\Http\Response;
use EmsApi\Params;
use EmsApi\Test\Base;

class ResponseTest extends Base
{
    /**
     * Helper method to create a mock Request object
     *
     * @param array $params
     * @return Request
     * @throws \Exception
     */
    private function createMockRequest(array $params = []): Request
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $request->params = new Params($params);
        
        return $request;
    }

    /**
     * @return void
     * @throws \ReflectionException
     * @throws \Exception
     */
    final public function testConstructor()
    {
        $request = $this->createMockRequest(['url' => 'https://example.com']);
        $response = new Response($request);
        
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('https://example.com', $response->url);
    }

    /**
     * @return void
     * @throws \ReflectionException
     * @throws \Exception
     */
    final public function testSetHttpCode()
    {
        $request = $this->createMockRequest();
        $response = new Response($request);
        
        $result = $response->setHttpCode(200);
        
        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(200, $response->getHttpCode());
        $this->assertEquals('OK', $response->httpMessage);
    }

    /**
     * @return void
     * @throws \ReflectionException
     * @throws \Exception
     */
    final public function testSetHttpCodeWithVariousStatuses()
    {
        $request = $this->createMockRequest();
        $response = new Response($request);
        
        // Test 201 Created
        $response->setHttpCode(201);
        $this->assertEquals(201, $response->getHttpCode());
        $this->assertEquals('Created', $response->httpMessage);
        
        // Test 404 Not Found
        $response->setHttpCode(404);
        $this->assertEquals(404, $response->getHttpCode());
        $this->assertEquals('Not Found', $response->httpMessage);
        
        // Test 500 Internal Server Error
        $response->setHttpCode(500);
        $this->assertEquals(500, $response->getHttpCode());
        $this->assertEquals('Internal Server Error', $response->httpMessage);
    }

    /**
     * @return void
     * @throws \ReflectionException
     * @throws \Exception
     */
    final public function testSetHttpCodeUnknownStatus()
    {
        $request = $this->createMockRequest();
        $response = new Response($request);
        
        $response->setHttpCode(999);
        $this->assertEquals(999, $response->getHttpCode());
        $this->assertNull($response->httpMessage);
    }

    /**
     * @return void
     * @throws \ReflectionException
     * @throws \Exception
     */
    final public function testGetIsCurlError()
    {
        $request = $this->createMockRequest();
        $response = new Response($request);
        
        // No curl error
        $response->curlCode = 0;
        $this->assertFalse($response->getIsCurlError());
        
        // With curl error
        $response->curlCode = 7; // CURLE_COULDNT_CONNECT
        $this->assertTrue($response->getIsCurlError());
    }

    /**
     * @return void
     * @throws \ReflectionException
     * @throws \Exception
     */
    final public function testGetIsHttpError()
    {
        $request = $this->createMockRequest();
        $response = new Response($request);
        
        // Success codes (2xx)
        $response->setHttpCode(200);
        $this->assertFalse($response->getIsHttpError());
        
        $response->setHttpCode(201);
        $this->assertFalse($response->getIsHttpError());
        
        // Client errors (4xx)
        $response->setHttpCode(400);
        $this->assertTrue($response->getIsHttpError());
        
        $response->setHttpCode(404);
        $this->assertTrue($response->getIsHttpError());
        
        // Server errors (5xx)
        $response->setHttpCode(500);
        $this->assertTrue($response->getIsHttpError());
        
        // Informational (1xx)
        $response->setHttpCode(100);
        $this->assertTrue($response->getIsHttpError());
        
        // Redirects (3xx)
        $response->setHttpCode(301);
        $this->assertTrue($response->getIsHttpError());
    }

    /**
     * @return void
     * @throws \ReflectionException
     * @throws \Exception
     */
    final public function testGetIsSuccess()
    {
        $request = $this->createMockRequest();
        $response = new Response($request);
        
        // Successful response
        $response->setHttpCode(200);
        $response->curlCode = 0;
        $this->assertTrue($response->getIsSuccess());
        
        // HTTP error
        $response->setHttpCode(404);
        $response->curlCode = 0;
        $this->assertFalse($response->getIsSuccess());
        
        // Curl error
        $response->setHttpCode(200);
        $response->curlCode = 7;
        $this->assertFalse($response->getIsSuccess());
        
        // Both errors
        $response->setHttpCode(500);
        $response->curlCode = 7;
        $this->assertFalse($response->getIsSuccess());
    }

    /**
     * @return void
     * @throws \ReflectionException
     * @throws \Exception
     */
    final public function testGetIsError()
    {
        $request = $this->createMockRequest();
        $response = new Response($request);
        
        // Successful response
        $response->setHttpCode(200);
        $response->curlCode = 0;
        $this->assertFalse($response->getIsError());
        
        // HTTP error
        $response->setHttpCode(404);
        $response->curlCode = 0;
        $this->assertTrue($response->getIsError());
        
        // Curl error
        $response->setHttpCode(200);
        $response->curlCode = 7;
        $this->assertTrue($response->getIsError());
    }

    /**
     * @return void
     * @throws \ReflectionException
     * @throws \Exception
     */
    final public function testGetMessageWithCurlError()
    {
        $request = $this->createMockRequest();
        $response = new Response($request);
        
        $response->curlCode = 7;
        $response->curlMessage = 'Could not connect to server';
        
        $this->assertEquals('Could not connect to server', $response->getMessage());
    }

    /**
     * @return void
     * @throws \ReflectionException
     * @throws \Exception
     */
    final public function testGetMessageWithHttpError()
    {
        $request = $this->createMockRequest();
        $response = new Response($request);
        
        $response->setHttpCode(404);
        $response->curlCode = 0;
        
        $this->assertEquals('Not Found', $response->getMessage());
    }

    /**
     * @return void
     * @throws \ReflectionException
     * @throws \Exception
     */
    final public function testGetMessageWithSuccess()
    {
        $request = $this->createMockRequest();
        $response = new Response($request);
        
        $response->setHttpCode(200);
        $response->curlCode = 0;
        
        $this->assertEquals('', $response->getMessage());
    }

    /**
     * @return void
     * @throws \ReflectionException
     * @throws \Exception
     */
    final public function testGetCodeWithCurlError()
    {
        $request = $this->createMockRequest();
        $response = new Response($request);
        
        $response->curlCode = 7;
        
        $this->assertEquals(7, $response->getCode());
    }

    /**
     * @return void
     * @throws \ReflectionException
     * @throws \Exception
     */
    final public function testGetCodeWithHttpError()
    {
        $request = $this->createMockRequest();
        $response = new Response($request);
        
        $response->setHttpCode(404);
        $response->curlCode = 0;
        
        $this->assertEquals(404, $response->getCode());
    }

    /**
     * @return void
     * @throws \ReflectionException
     * @throws \Exception
     */
    final public function testGetCodeWithSuccess()
    {
        $request = $this->createMockRequest();
        $response = new Response($request);
        
        $response->setHttpCode(200);
        $response->curlCode = 0;
        
        $this->assertEquals(0, $response->getCode());
    }

    /**
     * @return void
     * @throws \ReflectionException
     * @throws \Exception
     */
    final public function testPopulateWithoutCurlInfo()
    {
        $request = $this->createMockRequest();
        $response = new Response($request);
        
        $response->storeCurlInfo = false;
        $params = [
            'url' => 'https://example.com/api',
            'contentType' => 'application/json',
        ];
        
        $result = $response->populate($params);
        
        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals('https://example.com/api', $response->url);
        $this->assertEquals('application/json', $response->contentType);
        $this->assertNull($response->curlInfo);
    }

    /**
     * @return void
     * @throws \ReflectionException
     * @throws \Exception
     */
    final public function testPopulateWithCurlInfo()
    {
        $request = $this->createMockRequest();
        $response = new Response($request);
        
        $response->storeCurlInfo = true;
        $params = [
            'url' => 'https://example.com/api',
            'contentType' => 'application/json',
            'total_time' => 1.234,
            'size_download' => 1024,
        ];
        
        $result = $response->populate($params);
        
        $this->assertInstanceOf(Response::class, $result);
        $this->assertInstanceOf(Params::class, $response->curlInfo);
        $this->assertEquals(1.234, $response->curlInfo->itemAt('total_time'));
        $this->assertEquals(1024, $response->curlInfo->itemAt('size_download'));
    }

    /**
     * @return void
     * @throws \ReflectionException
     * @throws \Exception
     */
    final public function testBodyAsParams()
    {
        $request = $this->createMockRequest([
            'body' => new Params(['status' => 'success', 'data' => ['id' => 123]])
        ]);
        $response = new Response($request);
        
        $this->assertInstanceOf(Params::class, $response->body);
        $this->assertEquals('success', $response->body->itemAt('status'));
    }

    /**
     * @return void
     * @throws \ReflectionException
     * @throws \Exception
     */
    final public function testHeadersAsParams()
    {
        $request = $this->createMockRequest([
            'headers' => new Params(['Content-Type' => 'application/json', 'X-Api-Version' => '1.0'])
        ]);
        $response = new Response($request);
        
        $this->assertInstanceOf(Params::class, $response->headers);
        $this->assertEquals('application/json', $response->headers->itemAt('Content-Type'));
        $this->assertEquals('1.0', $response->headers->itemAt('X-Api-Version'));
    }

    /**
     * @return void
     * @throws \ReflectionException
     * @throws \Exception
     */
    final public function testAllStatusTexts()
    {
        $this->assertArrayHasKey(200, Response::$statusTexts);
        $this->assertEquals('OK', Response::$statusTexts[200]);
        
        $this->assertArrayHasKey(404, Response::$statusTexts);
        $this->assertEquals('Not Found', Response::$statusTexts[404]);
        
        $this->assertArrayHasKey(500, Response::$statusTexts);
        $this->assertEquals('Internal Server Error', Response::$statusTexts[500]);
        
        // Test a few RFC-specific ones
        $this->assertArrayHasKey(418, Response::$statusTexts);
        $this->assertEquals('I\'m a teapot', Response::$statusTexts[418]);
    }

    /**
     * @return void
     * @throws \ReflectionException
     * @throws \Exception
     */
    final public function testCurlErrorPriority()
    {
        $request = $this->createMockRequest();
        $response = new Response($request);
        
        // When both curl and http errors exist, curl error takes priority
        $response->curlCode = 28; // CURLE_OPERATION_TIMEDOUT
        $response->curlMessage = 'Operation timed out';
        $response->setHttpCode(500);
        
        $this->assertTrue($response->getIsCurlError());
        $this->assertTrue($response->getIsHttpError());
        $this->assertFalse($response->getIsSuccess());
        
        // getMessage should return curl message
        $this->assertEquals('Operation timed out', $response->getMessage());
        
        // getCode should return curl code
        $this->assertEquals(28, $response->getCode());
    }
}
