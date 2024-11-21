<?php declare(strict_types = 1);

namespace Sloganator;

class Database extends \SQLite3  {
    const string DB_FILE = "./sloganator.db";
    
    public function __construct(string $dbFile = self::DB_FILE) {
        $firstRun = !file_exists($dbFile);
        
        $this->open($dbFile);
        $this->exec("PRAGMA journal_mode = wal;");
        $this->exec("PRAGMA synchronous = NORMAL;");
        $this->exec("PRAGMA busy_timeout = 5000;");
        $this->exec("PRAGMA temp_store = memory");
        $this->exec("PRAGMA cache_size = 1000000000;");

        if ($firstRun) {
            $this->installSchema();
        }
        
        $this->enableExceptions(true);
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