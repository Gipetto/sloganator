<?php declare(strict_types = 1);

namespace Sloganator;

use Carbon\Carbon;

class Throttle {
    const THROTTLE = 15;
    
    public function __construct(private \SQLite3 $db) {}
    
    public function get(User $user): int {
        $ts = $this->getLastUserTimestamp($user->getUserId());
        $remaining = $ts - ((int) Carbon::now()->timestamp - self::THROTTLE);
        return max($remaining, 0);
    }
    
    private function getLastUserTimestamp(int $userId): int {
        $select = <<<SEL
            SELECT timestamp
            FROM throttles
            WHERE userid = :uid
            SEL;

        try {
            /**
             * @var \SQLite3Stmt $statement
             */
            $statement = $this->db->prepare($select);
            $statement->bindValue(":uid", $userId);

            /**
             * @var \SQLite3Result $result
             */
            $result = $statement->execute();

            /**
             * @var array<string, mixed> $data
             */
            $data = $result->fetchArray(SQLITE3_ASSOC);

            $result->finalize();
            $statement->close();
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
            return 0;
        }

        if (!$data) {
            return 0;
        }

        return (int) $data["timestamp"];
    }

    public function update(User $user): void {
        $upsert = <<<INS
            INSERT OR REPLACE INTO throttles 
            (userid, timestamp)
            VALUES(:uid, :ts)
            INS;
        
        try {
            /**
             * @var \SQLite3Stmt $u_statement
             */
            $u_statement = $this->db->prepare($upsert);
            
            $u_statement->bindValue(":uid", $user->getUserId());
            $u_statement->bindValue(":ts", Carbon::now()->timestamp);
    
            $u_statement->execute();
            $u_statement->close();
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
        }
    }
}