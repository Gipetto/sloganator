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
$i = 1;
while ($i <= 500) {
    $ii = 1;
    while ($ii <= 100) {
        $router->route("/v1/" . $i . "/" . $ii, "GET", fn(Request $rq) => new ApiResponse(200, (object) ["self" => "/v1/" . $i . "/" . $ii]));
        $ii++;
        $y++;
    }
    $i++;
}

print("Loaded " . $y . " routes in " . (microtime(true) - $startTime));

$response = $router->dispatch(new Request("GET", "/v1/77/88", ["foo" => "bar"]));

print_r($response);