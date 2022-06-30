<?php

use \PHPUnit\Framework\TestCase;
use Sloganator\Responses\{ApiResponse, PageResponse, TooManyRequests, Unauthorized, ValidationError};

class ResponseTest extends TestCase {

    public function assertResponse($response, $expectedCode, $expectedContent, $expectedHeaders) {
        ob_start();
        $response->respond();
        $responseContent = ob_get_clean();

        $this->assertEquals($expectedCode, http_response_code());
        $this->assertEquals($expectedContent, $responseContent);

        $headers = xdebug_get_headers();
		foreach($expectedHeaders as $header) {
			$this->assertContains($header, $expectedHeaders);
		}
    }

    /**
     * Run in separate process to capture headers
     * @runInSeparateProcess
     */
    public function testBasicResponse() {
        $response = new ApiResponse(200, (object) ["foo" => "bar"]);
        $this->assertEquals("200 OK", $response->getCodeString());

        $expectedContent = '{"foo":"bar"}';
        $expectedHeaders = [
            "Content-Type: application/json;charset=UTF-8",
            "Cache-Control: no-cache"
        ];
        $this->assertResponse($response, 200, $expectedContent, $expectedHeaders);
    }

    /**
     * Run in separate process to capture headers
     * @runInSeparateProcess
     */
    public function testTooManyRequests() {
        $response = new TooManyRequests(999);
        $this->assertEquals("429 Too Many Requests", $response->getCodeString());

        $expectedContent = '{"code":429,"message":"Hang loose. Slow and steady wins the race."}';
        $expectedHeaders = [
            "Content-Type: application/json;charset=UTF-8",
            "Cache-Control: no-cache",
            "Retry-After: 999"
        ];
        $this->assertResponse($response, 429, $expectedContent, $expectedHeaders);
    }

    /**
     * Run in separate process to capture headers
     * @runInSeparateProcess
     */
    public function testUnauthorized() {
        $response = new Unauthorized;
        $this->assertEquals("401 Unauthorized", $response->getCodeString());

        $expectedContent = '{"code":401,"message":"You must be logged in to create a slogan"}';
        $expectedHeaders = [
            "Content-Type: application/json;charset=UTF-8",
            "Cache-Control: no-cache"            
        ];
        $this->assertResponse($response, 401, $expectedContent, $expectedHeaders);
    }

    /**
     * Run in separate process to capture headers
     * @runInSeparateProcess
     */
    public function testValidationError() {
        $response = new ValidationError("That rug really tied the room together.");
        $this->assertEquals("400 Bad Request", $response->getCodeString());

        $expectedContent = '{"code":400,"message":"That rug really tied the room together."}';
        $expectedHeaders = [
            "Content-Type: application/json;charset=UTF-8",
            "Cache-Control: no-cache"          
        ];
        $this->assertResponse($response, 400, $expectedContent, $expectedHeaders);
    }

    /**
     * Run in separate process to capture headers
     * @runInSeparateProcess
     */
    public function testPageResponse() {
        $response = new PageResponse(200, "test-template", ["testParam" => "foo"]);
        $response->setTemplatesDir("test/fixtures/");
        $this->assertEquals("200 OK", $response->getCodeString());
        $this->assertEquals("text/html", $response->getContentType());

        $expectedContent = '<html><body><p>foo</p></body></html>';
        $expectedHeaders = [
            "Content-Type: text/html;charset=UTF-8"
        ];
        $this->assertResponse($response, 200, $expectedContent, $expectedHeaders);
    }
}