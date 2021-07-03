<?php

error_reporting(E_ALL | E_STRICT | E_WARNING);
ini_set("display_errors", 1);

define("PROJECT_ROOT", dirname(dirname(__FILE__)));

require_once PROJECT_ROOT . DIRECTORY_SEPARATOR . "vendor/autoload.php";
