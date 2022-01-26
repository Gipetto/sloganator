<?php declare(strict_types = 1);

require_once("vendor/autoload.php");
require_once("lib/TrieRouter/Router.php");
require_once("lib/Responses/HttpResponse.php");
require_once("lib/Responses/ApiResponse.php");

use Sloganator\Responses\ApiResponse;
use Sloganator\TrieRouter\{Request, Router};

$router = new Router;

$startTime = microtime(true);

$y = 0;
$i = 0;
while (++$i <= 500) {
    $ii = 0;
    while (++$ii <= 100) {
        $router->get("/v1/" . $i . "/" . $ii, fn(Request $rq) => new ApiResponse(200, (object) ["self" => "/v1/" . $i . "/" . $ii]));
        $y++;
    }
}

print("Loaded " . $y . " routes in " . (microtime(true) - $startTime));

$response = $router->dispatch(new Request(Request::GET, "/v1/77/88", ["foo" => "bar"]));

print_r($response);
