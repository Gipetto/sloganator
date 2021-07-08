<?php

use \PHPUnit\Framework\TestCase;
use \Sloganator\Router\{Router, RouteParams};
use \Sloganator\Responses\ApiResponse;

class RouterTest extends TestCase {
    protected $_SERVER;
    
    protected function setUp(): void {
        $this->_SERVER = $_SERVER;
    }

    protected function tearDown(): void {
        $_SERVER = $this->_SERVER;
    }

    public function routerTestProvider() {
        return [
            [   // Test Get Success
                "path" => "/v1/test",
                "REQUEST_URI" => "/v1/test",
                "callback" => fn(array $params) => new ApiResponse(200, (object) $params),
                "assertions" => [
                    "getCodeString" => "200 OK",
                    "getContent" => "{}"
                ]
            ],
            [   // Test Get Success With Params
                "path" => "/v1/test",
                "REQUEST_URI" => "/v1/test?foo=bar&bing=bang",
                "callback" => fn(array $params) => new ApiResponse(200, (object) $params),
                "assertions" => [
                    "getCodeString" => "200 OK",
                    "getContent" => '{"foo":"bar","bing":"bang"}'
                ]
            ],
            [   // Test Get Not Found
                "path" => "/v1/test",
                "REQUEST_URI" => "/foo",
                "callback" => fn(array $params) => new ApiResponse(200, (object) $params),
                "assertions" => [
                    "getCodeString" => "404 Not Found",
                    "getContent" => '{"code":404,"message":"Invalid Route"}'
                ]
            ],
            [   // Test Get Exception as Internal Server Error
                "path" => "/v1/test",
                "REQUEST_URI" => "/v1/test",
                "callback" => fn(array $params) => throw new Exception("foo"),
                "assertions" => [
                    "getCodeString" => "500 Internal Server Error",
                    "getContent" => '{"code":500,"message":"Internal Service Error"}'
                ]
            ]
        ]; 
    }

    /**
     * @dataProvider routerTestProvider
     */
    public function testRouterGet($path, $REQUEST_URI, $callback, $assertions) {
        $_SERVER["REQUEST_URI"] = $REQUEST_URI;
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->route($path, "GET", $callback);

        $response = $router->dispatch();

        foreach ($assertions as $method => $expectedValue) {
            $this->assertEquals($expectedValue, call_user_func([$response, $method]), $method);
        }
    }

    public function testPostSuccess() {
        $path = "/v1/test";
        $method = "POST";

        $_SERVER["REQUEST_URI"] = $path;
        $_SERVER["REQUEST_METHOD"] = $method;

        $router = new Router;
        $router->setInputStream('data:application/json,{"foo":"bar"}');
        $router->route($path, $method, fn(array $params) => new ApiResponse(201, (object) $params));

        $response = $router->dispatch();

        $this->assertEquals("201 Created", $response->getCodeString(), 'getCodeString');
        $this->assertEquals('{"body":{"foo":"bar"}}', $response->getContent(), 'getContent');
    }

    public function testRouterSubdir() {
        $_SERVER["REQUEST_URI"] = "/v1/foo/bar?bing=baz";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router("/v1/foo");
        $router->route("/bar", "GET", fn(array $params) => new ApiResponse(200, (object) $params));

        $response = $router->dispatch();
        $this->assertEquals("200 OK", $response->getCodeString());
        $this->assertEquals('{"bing":"baz"}', $response->getContent());
    }
}
