<?php declare(strict_types = 1);

require_once BASEDIR . "/lib/database.php";

class Slogan {
    public int $rowid;
    public int $timestamp;
    public string $username;
    public int $userid;
    public string $slogan;

    /**
     * @param array<string, mixed> $row
     */
    public function __construct(array $row) {
        $this->rowid = $row["rowid"];
        $this->timestamp = $row["timestamp"];
        $this->username = $row["username"];
        $this->userid = $row["userid"];
        $this->slogan = $row["slogan"];
    }
}

class Author {
    /**
     * @var int
     */
    public int $userid;

    /**
     * @var string[]
     */
    public array $usernames;

    /**
     * @param int $userid
     * @param string[] $usernames
     */
    public function __construct(int $userid, array $usernames = []) {
        $this->userid = $userid;
        $this->usernames = $usernames;
    }

    public function addUsername(string $username): void {
        array_push($this->usernames, $username);
    }
}

class ListMeta {
    public int $page;
    public int $pageSize;
    public int $results;
    public ?int $previousPage;
    public ?int $nextPage;

    /**
     * @var array<string, string|int>
     */
    public ?array $filter;

    /**
     * @param array<string, mixed> $params
     */
    public function __construct(array $params) {
        $this->page = $params["page"];
        $this->pageSize = $params["pageSize"];
        $this->results = $params["results"];
        $this->previousPage = $params["previousPage"];
        $this->nextPage = $params["nextPage"];
        $this->filter = $params["filter"];
    }
}

/**
 * @template TValue
 * @template-extends \ArrayObject<int, Author>
 */
class AuthorList extends \ArrayObject implements \JsonSerializable {
    public function offsetSet($key, $value) {
        if (!($value instanceof Author)) {
            throw new \InvalidArgumentException("Value must be of type Author");
        }

        parent::offsetSet($key, $value);
    }

    /**
     * Discard the numeric Ids in favor of a simple list when JSON serializing
     * Slightly magical, and nobody likes surprises :(
     * @return Author[]
     */
    public function jsonSerialize(): array {
        return array_values($this->getArrayCopy());
    }
}

/**
 * Super Janky™
 * @TODO implement an Either like construct instead
 */
class SloganResult {}

class SloganList extends SloganResult {
    /**
     * @var Slogan[]
     */
    public array $slogans;

    /**
     * @var ListMeta
     */
    public ListMeta $meta;

    /**
     * @param array<Slogan> $slogans
     * @param ListMeta $meta
     */
    public function __construct(array $slogans, ListMeta $meta) {
        $this->slogans = $slogans;
        $this->meta = $meta;
    }
}

class SloganError extends SloganResult {
    public int $code;
    public string $message;

    public function __construct(int $code, string $message) {
        $this->code = $code;
        $this->message = $message;
    }
}

class Sloganator {
    const PARAM_PAGE_SIZE = "pageSize";
    const PARAM_PAGE = "page";
    const PARAM_AUTHOR = "author";
    const SLOGAN_LEN_LIMIT = 150;

    private SQLite3 $db;

    public function __construct(SQLite3 $db) {
        $this->db = $db;
    }

    /**
     * @return AuthorList<Author>
     */
    public function authors(): AuthorList {
        $select = <<<SEL
            SELECT DISTINCT username, userid
            FROM slogans
            WHERE userid > 0
            ORDER BY userid ASC
            SEL;

        /**
         * @var SQLite3Stmt $statement
         */
        $statement = $this->db->prepare($select);

        /**
         * @var SQLite3Result $result
         */
        $result = $statement->execute();

        $data = new AuthorList;
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $username = $row["username"];
            $userid = $row["userid"];

            if (!isset($data[$userid])) {
                $data[$userid] = new Author($userid);
            }

            $data[$userid]->addUsername($username);
        }

        return $data;
    }

    public function add(User $user, string $slogan): int {
        $insert = <<<INS
            INSERT INTO slogans 
            (timestamp, username, userid, slogan) 
            VALUES (:ts, :us, :uid, :sn)
            INS;

        /**
         * @var SQLite3Stmt $statement
         */
        $statement = $this->db->prepare($insert);
        $statement->bindValue(":ts", time());
        $statement->bindValue(":us", trim($user->getUserName()));
        $statement->bindValue(":uid", (int) $user->getUserId());
        $statement->bindValue(":sn", trim($slogan));

        /**
         * @var SQLite3Result $result
         */
        $result = $statement->execute();
        $id = $this->db->lastInsertRowId();

        $statement->close();
        
        return $id;
    }    

    /**
     * @param array<string, int|string> $params
     */
    public function list(array $params): SloganResult {
        $selectBase = <<<SEL
            SELECT rowid, timestamp, username, userid, slogan
            FROM slogans
            %where%
            ORDER BY rowid DESC
            LIMIT :offset, :limit
            SEL;

        $where = "";

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

        $author = false;
        if ($params[static::PARAM_AUTHOR]) {
            $author = (int) $params[static::PARAM_AUTHOR];
            $where = "WHERE userid = :userid";
        }

        $select = strtr($selectBase, ["%where%" => $where]);
        
        /**
         * @var SQLite3Stmt $statement
         */
        $statement = $this->db->prepare($select);
        $statement->bindValue(":limit", $limit, SQLITE3_INTEGER);
        $statement->bindValue(":offset", $offset, SQLITE3_INTEGER);
        if($author) {
            $statement->bindValue(":userid", $author, SQLITE3_INTEGER);
        }

        /**
         * @var SQLite3Result $result
         */
        $result = $statement->execute();

        if (!($result instanceof SQLite3Result)) {
            return $this->error();
        }

        $data = [];
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data[] = new Slogan($row);
        }

        $numResults = count($data);

        $filter = [];
        if ($author) {
            $filter["author"] = $author;
        }

        return new SloganList($data, new ListMeta([
            "page" => $page,
            "pageSize" => $limit,
            "results" => $numResults,
            "previousPage" => ($page > 1 && $numResults > 0 ? $page - 1 : null),
            "nextPage" => ($numResults == $limit && $numResults > 0 ? $page + 1 : null),
            "filter" => $filter            
        ]));
    }

    /**
     * @param int $rowId
     * @return Slogan
     */
    public function get(int $rowId): Slogan {
        $select = <<<SEL
            SELECT rowid, timestamp, username, userid, slogan
            FROM slogans
            WHERE rowid = :id
            SEL;

        /**
         * @var SQLite3Stmt $statement
         */
        $statement = $this->db->prepare($select);
        $statement->bindValue(":id", $rowId);

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

        return new Slogan($data);
    }

    /**
     * @return Slogan
     */
    public function getRandom(): Slogan {
        $select = <<<SEL
            SELECT rowid, timestamp, username, slogan 
            FROM slogans ORDER BY RANDOM() LIMIT 1
            SEL;

        /**
         * @var array<string, int|string> $data
         */
        $data = $this->db->querySingle($select, true);
        return new Slogan($data);
    }

    /**
     * @return SloganError
     */
    public function error(): SloganError {
        return new SloganError($this->db->lastErrorCode(), $this->db->lastErrorMsg());
    }

    public function __destruct() {
        $this->db->close();
    }
}
