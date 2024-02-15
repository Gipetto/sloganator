<?php declare(strict_types = 1);

use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . "/.env");

define("BASEDIR", dirname(__FILE__));
define("SLGNTR_VERSION", "1.2.0");

if (getenv("SLOGANATOR_LOCAL_DOCKER")) {
    define("DOCROOT", BASEDIR . "/_local/");
}
