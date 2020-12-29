<?php declare(strict_types = 1);

define("DOCROOT", dirname(dirname(__DIR__)) . "/lib/local/");

if (preg_match("/\.(?:css|js)$/", $_SERVER["REQUEST_URI"])) {
    return false;
} elseif (preg_match("/local\/index\.html$/", $_SERVER["REQUEST_URI"])) {
    ob_start(); 
    include_once "lib/local/index.html";
    $output = ob_get_clean();
    header("Content-Type: text/html");
    echo $output;
} else {
    include dirname(dirname(__DIR__)) . "/index.php";
}