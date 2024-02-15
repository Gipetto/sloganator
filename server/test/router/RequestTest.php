<?php

use \PHPUnit\Framework\TestCase;
use \Sloganator\Router\Request;

class RequestTest extends TestCase {
    protected $_SERVER;
    
    protected function setUp(): void {
        $this->_SERVER = $_SERVER;
    }

    protected function tearDown(): void {
        $_SERVER = $this->_SERVER;
    }

    public function testRequestGet() {
        $_SERVER["REQUEST_METHOD"] = "GET";
        $_SERVER["REQUEST_URI"] = "/foo/bar";

        $request = Request::new("");

        $this->assertEquals("GET", $request->method);
        $this->assertEquals("/foo/bar", $request->path);
        $this->assertEquals([], $request->params);
    }

    public function testRequestGetWithParams() {
        $_SERVER["REQUEST_METHOD"] = "GET";
        $_SERVER["REQUEST_URI"] = "/foo/bar?bing=baz&bar=1";
        $_SERVER["HTTP_Buckaroo_Bonzai"] = "No matter where you go, there you are.";

        $request = Request::new("");

        $this->assertEquals("GET", $request->method);
        $this->assertEquals("/foo/bar", $request->path);
        $this->assertEquals([
            "bing" => "baz",
            "bar" => "1"
        ], $request->params);
        $this->assertEquals([
            "Buckaroo-Bonzai" => "No matter where you go, there you are."
        ], $request->headers);
    }

    public function testRequestPost() {
        $_SERVER["REQUEST_METHOD"] = "POST";
        $_SERVER["REQUEST_URI"] = "/foo/bar/baz?one=two";

        $request = Request::new('data:application/json,{"bing": 1}');

        $this->assertEquals("POST", $request->method);
        $this->assertEquals("/foo/bar/baz", $request->path);
        $this->assertEquals([
            "one" => "two"
        ], $request->params);
        $this->assertEquals([
            "bing" => 1
        ], $request->body);
    }
}
