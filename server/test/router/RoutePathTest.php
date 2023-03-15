<?php

use \PHPUnit\Framework\TestCase;
use \Sloganator\Router\RoutePath;

class RoutePathTest extends TestCase {

    public function routePathProvider() {
        return [
            [
                "Empty Route is empty",
                "",
                True,
                []
            ],
            [
                "Slash Route is empty",
                "/",
                True,
                []
            ],
            [
                "Basic Route",
                "/foo/bar",
                False,
                ["foo", "bar"]
            ],
            [
                "Route with params",
                "/foo/bar?bing=baz",
                False,
                ["foo", "bar"]
            ],
            [
                "Route with empty part maintains empty parts",
                "/foo//bar",
                False,
                ["foo", "", "bar"]
            ]
        ];
    }

    /**
     * @dataProvider routePathProvider
     */
    public function testRoutePath($name, $route, $expectedEmpty, $expectedParts) {
        $rp = new RoutePath($route);
        $this->assertEquals($expectedEmpty, $rp->isEmpty, $name);
        $this->assertEquals($expectedParts, $rp->parts, $name);
    }
}
