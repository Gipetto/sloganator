<?php declare(strict_types = 1);

define("DOCROOT", dirname(dirname(__DIR__)) . "/lib/local/");

if (preg_match("/\.(?:css|js)$/", $_SERVER["REQUEST_URI"])) {
    if (preg_match("|^/mies|", $_SERVER["REQUEST_URI"])) {
        $path = preg_replace("|^/mies/|", "", $_SERVER["REQUEST_URI"]);
        header("Content-Type: " . mime_content_type($path));
        return readfile($path);
    } else {
        return false;
    }
} elseif (preg_match("/local\/index\.html$/", $_SERVER["REQUEST_URI"])) {
    ob_start(); 
    include_once "lib/local/index.html";
    $output = ob_get_clean();
    header("Content-Type: text/html");
    echo $output;
} else {
    include dirname(dirname(__DIR__)) . "/index.php";
}
