<?php declare(strict_types = 1);

if (!defined("DOCROOT")) {
    define("DOCROOT", dirname(dirname(__DIR__)) . "/lib/local/");
}

if (preg_match("/\.(?:css|js)$/", $_SERVER["REQUEST_URI"])) {
    if (preg_match("|^/mies|", $_SERVER["REQUEST_URI"])) {
        $path = preg_replace("|^/mies/|", "", $_SERVER["REQUEST_URI"]);
        header("Content-Type: " . mime_content_type($path));
        return readfile($path);
    } else {
        return false;
    }
} elseif (preg_match("/local\/index\.html$/", $_SERVER["REQUEST_URI"])) {
    header("Content-Type: text/html");
    return readfile("lib/local/index.html");
} elseif (preg_match("/coverage.*/", $_SERVER["REQUEST_URI"])) {
    return false;
} else {
    include dirname(dirname(__DIR__)) . "/index.php";
}
