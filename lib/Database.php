<?php declare(strict_types = 1);

namespace Sloganator;

class Database extends \SQLite3  {
    const DB_FILE = "./sloganator.db";
    
    private bool $bare = false;

    public function __construct() {
        $firstRun = !file_exists(self::DB_FILE);
        
        $this->open(self::DB_FILE);

        if ($firstRun) {
            $this->installSchema();
        }
    }

    public function installSchema(): void { 
        $slogans_create = <<<C1
            CREATE TABLE IF NOT EXISTS slogans (
                timestamp INTEGER,
                username VARCHAR(100),
                userid INTEGER,
                slogan TEXT
            )
            C1;
        $this->exec($slogans_create);
    
        $throttles_create = <<<C2
            CREATE TABLE IF NOT EXISTS throttles (
                userid INTEGER UNIQUE,
                timestamp INTEGER
            )
            C2;
        $this->exec($throttles_create);

        $slogans_index_create = <<<I1
            CREATE INDEX IF NOT EXISTS user_index
            ON slogans (userid);
            I1;
        $this->exec($slogans_index_create);
    }
}