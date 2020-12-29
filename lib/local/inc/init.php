<?php declare(strict_types = 1);

/**
 * Fake loading of mybb to get user id & name
 */

define("MYBB_ROOT", DOCROOT);

$mybb = new StdClass;
$mybb->user = [
    "username" => "Tex",
    "uid" => 1
];