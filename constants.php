<?php declare(strict_types = 1);

define("BASEDIR", dirname(__FILE__));
define("SLGNTR_VERSION", "1.1.1");

if (getenv("SLOGANATOR_LOCAL_DOCKER")) {
    define("DOCROOT", BASEDIR . "/_local/");
}