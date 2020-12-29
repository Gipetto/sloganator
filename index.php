<?php declare(strict_types = 1);

define("BASEDIR", dirname(__FILE__));

require_once BASEDIR . "/lib/responses.php";
require_once BASEDIR . "/lib/router.php";
require_once BASEDIR . "/lib/user.php";
require_once BASEDIR . "/lib/throttle.php";
require_once BASEDIR . "/lib/sloganator.php";

// sigh... gotta keep MyBB in line
ob_start();

$user = new User;
$db = new Database;
$sloganator = new Sloganator($db);
$throttle = new Throttle($db);

$router = new Router("/mies/sloganator");

$router->route("/", "GET", function($params) use ($sloganator, $user) {
    return new PageResponse(200, 'browse', [
        "userId" => $user->getUserId(),
        "userName" => $user->getUserName()
    ]);
});

$router->route("/v1/slogans", "GET", function($params) use ($sloganator) {    
	$slogans = $sloganator->list($params);
	return new \ApiResponse(200, $slogans);
});

$router->route("/v1/slogans", "POST", function($params) use ($sloganator, $user, $throttle) {
    if (!$user->isLoggedIn()) {
        return new Unauthorized;
    }

    $rt = $throttle->get($user);
    if ($rt) {
        return TooManyRequests($rt);
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
        return new ApiResponse(201, $slogan);    
    }
});

$response = $router->dispatch();

ob_end_clean();

$response->respond();
