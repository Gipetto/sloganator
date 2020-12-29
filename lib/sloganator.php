<?php declare(strict_types = 1);

require_once BASEDIR . "/lib/database.php";

class Sloganator {
    const PARAM_PAGE_SIZE = "pageSize";
    const PARAM_PAGE = "page";
    const SLOGAN_LEN_LIMIT = 150;

    private $db;

    public function __construct(SQLite3 $db) {
        $this->db = $db;
    }

    public function add(User $user, string $slogan): int {
        $insert = <<<INS
            INSERT INTO slogans 
            (timestamp, username, userid, slogan) 
            VALUES (:ts, :us, :uid, :sn)
            INS;

        $statement = $this->db->prepare($insert);
        $statement->bindValue(":ts", time());
        $statement->bindValue(":us", trim($user->getUserName()));
        $statement->bindValue(":uid", (int) $user->getUserId());
        $statement->bindValue(":sn", trim($slogan));

        $result = $statement->execute();
        $id = $this->db->lastInsertRowId();

        $statement->close();
        
        return $id;
    }    

    public function list(array $params): array {
        $select = <<<SEL
            SELECT rowid, timestamp, username, userid, slogan
            FROM slogans
            ORDER BY rowid DESC
            LIMIT :offset, :limit
            SEL;

        $limit = 100;
        if ($params[static::PARAM_PAGE_SIZE]) {
            $limit = (int) $params[static::PARAM_PAGE_SIZE];
        }

        $offset = 0;
        $page = 1;
        if ($params[static::PARAM_PAGE]) {
            $page = (int) $params[static::PARAM_PAGE];
            $offset = abs($page - 1) * $limit;
        }

        $statement = $this->db->prepare($select);
        $statement->bindValue(":limit", $limit, SQLITE3_INTEGER);
        $statement->bindValue(":offset", $offset, SQLITE3_INTEGER);
        $result = $statement->execute();

        if (!$result) {
            return $this->error();
        }

        $data = [];
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }

        $numResults = count($data);

        return [
            "slogans" => $data,
            "meta" => [
                "page" => $page,
                "pageSize" => $limit,
                "results" => $numResults,
                "previousPage" => ($page > 1 && $numResults > 0 ? $page - 1 : null),
                "nextPage" => ($numResults == $limit && $numResults > 0 ? $page + 1 : null)
            ]
        ];
    }

    public function get($rowId): array {
        $select = <<<SEL
            SELECT rowid, timestamp, username, userid, slogan
            FROM slogans
            WHERE rowid = :id
            SEL;

        $statement = $this->db->prepare($select);
        $statement->bindValue(":id", $rowId);
        
        $result = $statement->execute();
        $data = $result->fetchArray(SQLITE3_ASSOC);
    
        $result->finalize();
        $statement->close();

        return $data;
    }

    public function getRandom(): array {
        $select = <<<SEL
            SELECT rowid, timestamp, username, slogan 
            FROM slogans ORDER BY RANDOM() LIMIT 1
            SEL;
        return $this->db->querySingle($select, true);
    }

    public function error(): array {
        return [
            "code" => $this->db->lastErrorCode(),
            "message" => $this->db->lastErrorMsg()
        ];
    }

    public function __destruct() {
        $this->db->close();
    }
}
