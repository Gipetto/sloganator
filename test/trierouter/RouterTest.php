<?php

use \PHPUnit\Framework\TestCase;
use Sloganator\Responses\ApiResponse;
use \Sloganator\TrieRouter\{InvalidMethodException, Request, Router};

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
                "callback" => fn(Request $request) => new ApiResponse(200, (object) $request),
                "assertions" => [
                    "getCodeString" => "200 OK",
                    "getContent" => '"params":[]'
                ]
            ],
            [   // Test Get Success With Params
                "path" => "/v1/test",
                "REQUEST_URI" => "/v1/test?foo=bar&bing=bang",
                "callback" => fn(Request $request) => new ApiResponse(200, (object) $request),
                "assertions" => [
                    "getCodeString" => "200 OK",
                    "getContent" => '"params":{"foo":"bar","bing":"bang"}'
                ]
            ],
            [   // Test Get Not Found
                "path" => "/v1/test",
                "REQUEST_URI" => "/foo",
                "callback" => fn(Request $request) => new ApiResponse(200, (object) $request),
                "assertions" => [
                    "getCodeString" => "404 Not Found",
                    "getContent" => '{"code":404,"message":"Invalid Route"}'
                ]
            ],
            [   // Test Get Exception as Internal Server Error
                "path" => "/v1/test",
                "REQUEST_URI" => "/v1/test",
                "callback" => fn(Request $request) => throw new Exception("foo"),
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

        $router = new Router;
        $router->route($path, "GET", $callback);

        $response = $router->dispatch(Request::new(""));

        foreach($assertions as $method => $expectedValue) {
            $this->assertStringContainsString($expectedValue, call_user_func([$response, $method]), $method);
        }
    }

    public function testMethodNotAllowed() {
        $router = new Router;
        $router->route("/v1/test", "GET", fn(Request $rq) => new ApiResponse(200, $rq));

        $response = $router->dispatch(new Request("POST", "/v1/test"));
        $this->assertEquals("405 Method Not Allowed", $response->getCodeString());
    }

    public function testPostJsonSuccess() {
        $path = "/v1/test";
        $method = "POST";

        $_SERVER["REQUEST_URI"] = $path;
        $_SERVER["REQUEST_METHOD"] = $method;

        $router = new Router;
        $router->route($path, $method, fn(Request $rq) => new ApiResponse(201, (object) $rq));

        $response = $router->dispatch(Request::new('data:application/json,{"bing": 1}'));

        $this->assertEquals("201 Created", $response->getCodeString());

        $json = json_decode($response->getContent());
        $this->assertEquals((object) ["bing" => 1], $json->params->body);
    }

    public function testGetPostOnSamePath() {
        $path = "/v1/test";

        $router = new Router;
        $router->route($path, "GET", fn(Request $rq) => new ApiResponse(200, $rq));
        $router->route($path, "POST", fn(Request $rq) => new ApiResponse(201, $rq));

        $_SERVER["REQUEST_URI"] = $path;
        $_SERVER["REQUEST_METHOD"] = "GET";

        $response = $router->dispatch(Request::new(""));
        $this->assertEquals("200 OK", $response->getCodeString());

        $_SERVER["REQUEST_METHOD"] = "POST";
        $response = $router->dispatch(Request::new('data:application/json,{"bing": 1}'));
        $this->assertEquals("201 Created", $response->getCodeString());
    }

    public function testRouteInvalidMethod() {
        $this->expectException(InvalidMethodException::class);

        $router = new Router;
        $router->route("/v1/test", "DEFENESTRATE", fn(Request $rq) => new ApiResponse(200, $rq));
    }

    public function testRootNodeGet() {
        $router = new Router;
        $router->route("/", "GET", fn(Request $rq) => new ApiResponse(200, $rq));
        $response = $router->dispatch(new Request("GET", "/", ["foo" => "bar", "bing" => "bang"]));

        $this->assertEquals("200 OK", $response->getCodeString());
        $this->assertStringContainsString('"params":{"foo":"bar","bing":"bang"}', $response->getContent());
    }

    public function testEmptyRootNode() {
        $router = new Router;
        $response = $router->dispatch(new Request("GET", "/"));
        
        $this->assertEquals("404 Not Found", $response->getCodeString());
    }

    // public function testLotsOfNodes() {
    //     $router = new Router;
        
    //     $i = 1;
    //     while ($i < 500) {
    //         $ii = 1;
    //         while ($ii < 100) {
    //             $router->route("/v1/" . $i . "/" . $ii, "GET", fn(Request $rq) => new ApiResponse(200, (object) ["self" => "/v1/" . $i . "/" . $ii]));
    //             $ii++;
    //         }
    //         $i++;
    //     }

    //     $response = $router->dispatch(new Request("GET", "/v1/77/88", ["foo" => "bar"]));
    //     $this->assertEquals("200 OK", $response->getCodeString());
    //     $this->assertEquals('{"self":"\/v1\/77\/88"}', $response->getContent());
    // }
}