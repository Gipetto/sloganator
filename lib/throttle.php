<?php declare(strict_types = 1);

require_once BASEDIR . "/lib/database.php";

class Throttle {
    const THROTTLE = 15;

    /**
     * @var SQLite3
     */
    private $db;
    
    public function __construct(SQLite3 $db) {
        $this->db = $db;
    }
    
    public function get(\User $user): int {
        $ts = $this->getLastUserTimestamp($user->getUserId());
        $remaining = $ts - (time() - self::THROTTLE);
        return max($remaining, 0);
    }
    
    private function getLastUserTimestamp(int $userId): int {
        $select = <<<SEL
            SELECT timestamp
            FROM throttles
            WHERE userid = :uid
            SEL;

        /**
         * @var SQLite3Stmt $statement
         */
        $statement = $this->db->prepare($select);
        $statement->bindValue(":uid", $userId);

        /**
         * @var SQLite3Result $result
         */
        $result = $statement->execute();

        /**
         * @var array<string, int|string> $data
         */
        $data = $result->fetchArray(SQLITE3_ASSOC);

        $result->finalize();
        $statement->close();

        return (int) $data["timestamp"];
    }

    public function update(\User $user): void {
        $upsert = <<<INS
            INSERT OR REPLACE INTO throttles 
            (userid, timestamp)
            VALUES(:uid, :ts)
            INS;
        
        /**
         * @var SQLite3Stmt $u_statement
         */
        $u_statement = $this->db->prepare($upsert);
        
        $u_statement->bindValue(":uid", $user->getUserId());
        $u_statement->bindValue(":ts", time());
 
        $u_statement->execute();
        $u_statement->close();
    }
}