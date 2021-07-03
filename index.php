<?php declare(strict_types = 1);

require_once("constants.php");

require_once BASEDIR . "/lib/responses.php";
require_once BASEDIR . "/lib/router.php";
require_once BASEDIR . "/lib/user.php";
require_once BASEDIR . "/lib/throttle.php";
require_once BASEDIR . "/lib/sloganator.php";
require_once BASEDIR . "/lib/caches.php";

// sigh... gotta keep MyBB in line
ob_start();

$router = new Router("/mies/sloganator");

$router->route("/", "GET", function(array $params) {
    $user = new User;
    return new PageResponse(200, 'browse', [
        "userId" => $user->getUserId(),
        "userName" => $user->getUserName()
    ]);
});

$router->route("/v1/authors", "GET", function(array $params) {
    $cache = new SuccessfulResponseCache("authors");
    $response = $cache->get();

    if (!($response instanceof Response)) {
        $db = new Database;
        $sloganator = new Sloganator($db);

        $authors = $sloganator->authors();
        $response = new ApiResponse(200, $authors);
        
        $cache->set($response);
    }

    return $response;
});

$router->route("/v1/slogans", "GET", function(array $params) {
    $db = new Database;
    $sloganator = new Sloganator($db);

    $result = $sloganator->list($params);
    return new ApiResponse(200, $result);
});

$router->route("/v1/slogans/latest", "GET", function(array $params) {
    $cache = new SuccessfulResponseCache("latest");
    $response = $cache->get();

    if (!($response instanceof Response)) {
        $db = new Database;
        $sloganator = new Sloganator($db);

        /**
         * @var SloganList $result
         */
        $result = $sloganator->list([
            "pageSize" => 1,
            "page" => 1
        ]);

        /**
         * @var Slogan $slogan
         */
        $slogan = current($result->slogans);

        $response = new ApiResponse(200, $slogan);
        $cache->set($response);
    }

    return $response;
});

$router->route("/v1/slogans", "POST", function(array $params) {
    $user = new User;
    $db = new Database;
    $sloganator = new Sloganator($db);
    $throttle = new Throttle($db);

    if (!$user->isLoggedIn()) {
        return new Unauthorized;
    }

    $rt = $throttle->get($user);
    if ($rt) {
        return new TooManyRequests($rt);
    }
    
    $slogan = trim($params["body"]["slogan"]);
    $sloganLen = mb_strlen($slogan);

    if ($sloganLen > Sloganator::SLOGAN_LEN_LIMIT) {
        return new ValidationError("Slogan must be " . Sloganator::SLOGAN_LEN_LIMIT . " characters or less");
    } elseif (!$sloganLen) {
        return new ValidationError("Slogan must not be empty");
    }

    $id = $sloganator->add($user, $slogan);
    $throttle->update($user);

    if (!$id) {
        $e = $sloganator->error();
        return new ApiResponse(400, $e);
    } else {
        $slogan = $sloganator->get($id);
        $response = new ApiResponse(201, $slogan);

        $latestCache = new SuccessfulResponseCache("latest");
        $latestCache->flush();

        $authorsCache = new SuccessfulResponseCache("authors");
        $authorsCache->flush();

        return $response;
    }
});

$response = $router->dispatch();

ob_end_clean();

$response->respond();
