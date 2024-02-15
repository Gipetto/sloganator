<?php

error_reporting(E_ALL | E_STRICT | E_WARNING);
ini_set("display_errors", 1);

$capture = tmpfile();
ini_set('error_log', stream_get_meta_data($capture)['uri']);

define("BASEDIR", dirname(__FILE__));
define("PROJECT_ROOT", dirname(dirname(__FILE__)));

require_once PROJECT_ROOT . DIRECTORY_SEPARATOR . "vendor/autoload.php";
require_once "SloganatorTestCase.php";
