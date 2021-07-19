<?php declare(strict_types = 1);

require_once("vendor/autoload.php");

use Sloganator\Database;
use Sloganator\Processors\WordCounter;

$wp = new WordCounter(function() {
    $db = new Database;
    
    $select = <<<SEL
        SELECT slogan
        FROM slogans
    SEL;

    /**
     * @var \SQLite3Result
     */
    $results = $db->query($select);

    while ($slogan = $results->fetchArray(SQLITE3_NUM)) {
        yield $slogan[0];
    }
});

echo json_encode(array_values($wp->run(true)), JSON_PRETTY_PRINT);