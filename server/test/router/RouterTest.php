<?php

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sloganator\Responses\{ApiResponse, NoContent};
use Sloganator\Router\{InvalidMethodException, Request, Router};

class RouterTest extends TestCase {
    protected $_SERVER;
    
    protected function setUp(): void {
        $this->_SERVER = $_SERVER;
		$_SERVER["SERVER_NAME"] = "localhost";
    }

    protected function tearDown(): void {
        $_SERVER = $this->_SERVER;
    }


    public static function routerTestProvider() {
        return [
            [   // Test Get Success
                "path" => "/v1/test",
                "REQUEST_URI" => "/v1/test",
                "callback" => fn(Request $request) => new ApiResponse(200, (object) $request),
                "pathPrefix" => null,
                "assertions" => [
                    "getCodeString" => "200 OK",
                    "getContent" => '"params":[]'
                ]
            ],
            [   // Test Get Success With Params
                "path" => "/v1/test",
                "REQUEST_URI" => "/v1/test?foo=bar&bing=bang",
                "callback" => fn(Request $request) => new ApiResponse(200, (object) $request),
                "pathPrefix" => null,
                "assertions" => [
                    "getCodeString" => "200 OK",
                    "getContent" => '"params":{"foo":"bar","bing":"bang"}'
                ]
            ],
            [   // Test Get Not Found
                "path" => "/v1/test",
                "REQUEST_URI" => "/foo",
                "callback" => fn(Request $request) => new ApiResponse(200, (object) $request),
                "pathPrefix" => null,
                "assertions" => [
                    "getCodeString" => "404 Not Found",
                    "getContent" => '{"code":404,"message":"Invalid Route"}'
                ]
            ],
            [   // Test Get Exception as Internal Server Error
                "path" => "/v1/test",
                "REQUEST_URI" => "/v1/test",
                "callback" => fn() => throw new Exception("foo"),
                "pathPrefix" => null,
                "assertions" => [
                    "getCodeString" => "500 Internal Server Error",
                    "getContent" => '{"code":500,"message":"Internal Service Error"}'
                ]
            ],
            [ // Test Get with pathPrefix
                "path" => "/v1/test",
                "REQUEST_URI" => "/foo/bar/v1/test",
                "callback" => fn(Request $request) => new ApiResponse(200, (object) $request),
                "pathPrefix" => "/foo/bar/", // trailing slash not required, setPathPrefix should remove it
                "assertions" => [
                    "getCodeString" => "200 OK",
                    "getContent" => '"params":[]'
                ],
            ]
        ]; 
    } 

    #[DataProvider("routerTestProvider")]
    public function testRouterGet($path, $REQUEST_URI, $callback, $pathPrefix, $assertions) {
        $_SERVER["REQUEST_URI"] = $REQUEST_URI;
        $_SERVER["REQUEST_METHOD"] = Request::GET;

        $router = new Router;

        if ($pathPrefix) {
            $router->setPathPrefix($pathPrefix);
        }

        $router->get($path, $callback);
        $response = $router->dispatch();

        foreach($assertions as $method => $expectedValue) {
            $this->assertStringContainsString($expectedValue, call_user_func([$response, $method]), $method);
        }
    }

    public function testRouterDelete() {
        $router = new Router;
        $router->delete("/v1/test", fn(Request $rq) => new ApiResponse(200, $rq));

        $response = $router->dispatch(new Request(Request::DELETE, "/v1/test"));
        $this->assertEquals("200 OK", $response->getCodeString());
    }

    public function testMethodNotAllowed() {
        $router = new Router;
        $router->get("/v1/test", fn(Request $rq) => new ApiResponse(200, $rq));

        $response = $router->dispatch(new Request(Request::POST, "/v1/test"));
        $this->assertEquals("405 Method Not Allowed", $response->getCodeString());
    }

    public function testGetOptionsNoContent() {
        $router = new Router;
        $router->get("/v1/test", fn(Request $rq) => new ApiResponse(200, $rq));
        
        $response = $router->dispatch(new Request(Request::OPTIONS, "/v1/test"));
        $this->assertEquals("405 Method Not Allowed", $response->getCodeString());
    }

    public function testPostOptionsNoContent() {
        $router = new Router;
        $router->post("/v1/test", fn(Request $rq) => new ApiResponse(200, $rq));
        
        $response = $router->dispatch(new Request(Request::OPTIONS, "/v1/test"));
        $this->assertEquals("204 No Content", $response->getCodeString());
    }

    public function testPutOptionsNoContent() {
        $router = new Router;
        $router->put("/v1/test", fn(Request $rq) => new ApiResponse(200, $rq));
        
        $response = $router->dispatch(new Request(Request::OPTIONS, "/v1/test"));
        $this->assertEquals("204 No Content", $response->getCodeString());
    }

    public function testDeleteOptionsNoContent() {
        $router = new Router;
        $router->delete("/v1/test", fn(Request $rq) => new ApiResponse(200, $rq));
        
        $response = $router->dispatch(new Request(Request::OPTIONS, "/v1/test"));
        $this->assertEquals("204 No Content", $response->getCodeString());
    }

    public function testPostJsonSuccess() {
        $path = "/v1/test";
        $method = Request::POST;

        $_SERVER["REQUEST_URI"] = $path;
        $_SERVER["REQUEST_METHOD"] = $method;

        $router = new Router;
        $router->post($path, fn(Request $rq) => new ApiResponse(201, (object) $rq));

        $response = $router->dispatch(Request::new('data:application/json,{"bing": 1}'));

        $this->assertEquals("201 Created", $response->getCodeString());

        $json = json_decode($response->getContent());
        $this->assertEquals((object) ["bing" => 1], $json->body);
    }

    public function testPutJsonSuccess() {
        $path = "/v1/test";
        $method = Request::PUT;

        $_SERVER["REQUEST_URI"] = $path;
        $_SERVER["REQUEST_METHOD"] = $method;

        $router = new Router;
        $router->put($path, fn(Request $rq) => new NoContent);

        $response = $router->dispatch(Request::new('data:application/json,{"bing":"bang"}'));

        $this->assertEquals("204 No Content", $response->getCodeString());
        $this->assertEquals("", $response->getContent());
    }

    public function testGetPostOnSamePath() {
        $path = "/v1/test";

        $router = new Router;
        $router->get($path, fn(Request $rq) => new ApiResponse(200, $rq));
        $router->post($path, fn(Request $rq) => new ApiResponse(201, $rq));

        $_SERVER["REQUEST_URI"] = $path;
        $_SERVER["REQUEST_METHOD"] = Request::GET;

        $response = $router->dispatch(Request::new(""));
        $this->assertEquals("200 OK", $response->getCodeString());

        $_SERVER["REQUEST_METHOD"] = Request::POST;
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
        $router->get("/", fn(Request $rq) => new ApiResponse(200, $rq));
        $response = $router->dispatch(new Request(Request::GET, "/", ["foo" => "bar", "bing" => "bang"]));

        $this->assertEquals("200 OK", $response->getCodeString());
        $this->assertStringContainsString('"params":{"foo":"bar","bing":"bang"}', $response->getContent());
    }

    public function testEmptyRootNode() {
        $router = new Router;
        $response = $router->dispatch(new Request(Request::GET, "/"));
        
        $this->assertEquals("404 Not Found", $response->getCodeString());
    }

    /**
     * Load up 10k routes and randomly look up a single route
     */
    public function testLotsOfNodes() {
        $r1limit = 50;
        $r2limit = 25;
		$r3limit = 10;

        $router = new Router;
        
        $i = 0;
        while (++$i <= $r1limit) {
            $ii = 0;
			$router->get("/v1/" . $i, fn() => new ApiResponse(200, (object) ["self" => "/v1/" . $i]));
            while (++$ii <= $r2limit) {
                $router->get("/v1/" . $i . "/" . $ii, fn() => new ApiResponse(200, (object) ["self" => "/v1/" . $i . "/" . $ii]));
				$iii = 0;
				while (++$iii <= $r3limit) {
					$router->get("/v1/" . $i . "/" . $ii . "/" . $iii, fn() => new ApiResponse(200, (object) ["self" => "/v1/" . $i . "/" . $ii . "/" . $iii]));
				}
			}
        }

        $r1 = mt_rand(1, $r1limit);
        $r2 = mt_rand(1, $r2limit);
		$r3 = mt_rand(1, $r3limit);

        $response = $router->dispatch(new Request(Request::GET, "/v1/" . $r1 . "/" . $r2 . "/" . $r3, ["foo" => "bar"]));
        $this->assertEquals("200 OK", $response->getCodeString());
        $this->assertEquals('{"self":"\/v1\/' . $r1 . '\/' . $r2 . '\/' . $r3 .'"}', $response->getContent());
    }

    public function testValidAddGetAllowedOrigin() {
        $router = new Router;
        $router->addAllowedOrigin("http://localhost");
        $this->assertEquals("http://localhost", $router->getAllowedOrigin());        
    }

    public function testInvalidAddGetAllowedOrigin() {
        $router = new Router;
        $router->addAllowedOrigin("https://foo.com");
        $this->assertEquals("", $router->getAllowedOrigin());        
    }
}
