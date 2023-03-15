<?php declare(strict_types = 1);

namespace Sloganator\Service;

use Carbon\Carbon;
use Sloganator\User;

class Sloganator {
    const PARAM_PAGE_SIZE = "pageSize";
    const PARAM_PAGE = "page";
    const PARAM_AUTHOR = "author";

    const SLOGAN_LEN_LIMIT = 250;

    const DEFAULT_PAGE = 1;
    const DEFAULT_PAGE_SIZE = 100;

    public function __construct(private \SQLite3 $db) {}

    /**
     * @TODO - Refactor AuthorList to something less magical
     * @return AuthorList<Author>
     */
    public function authors(): AuthorList|SloganError {
        $select = <<<SEL
            SELECT DISTINCT userid, username
            FROM slogans
            WHERE userid > 0
            ORDER BY userid ASC
            SEL;

        try {
            /**
             * @var \SQLite3Stmt $statement
             */
            $statement = $this->db->prepare($select);

            /**
             * @var \SQLite3Result $result
             */
            $result = $statement->execute();

            $data = new AuthorList;

            while ($row = $result->fetchArray()) {
                [$userid, $username] = $row;
                $data[$userid] ??= new Author(userid: $userid);
                $data[$userid]?->addUsername($username);
            }
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
            return $this->error();
        }

        return $data;
    }

    /**
     * @return int|SloganError
     */
    public function add(User $user, string $slogan): int|SloganError {
        $insert = <<<INS
            INSERT INTO slogans 
            (timestamp, username, userid, slogan) 
            VALUES (:ts, :us, :uid, :sn)
            INS;

        try {
            /**
             * @var \SQLite3Stmt $statement
             */
            $statement = $this->db->prepare($insert);
            $statement->bindValue(":ts", Carbon::now()->timestamp);
            $statement->bindValue(":us", trim($user->getUserName()));
            $statement->bindValue(":uid", (int) $user->getUserId());
            $statement->bindValue(":sn", trim($slogan));
            
            /**
             * @var \SQLite3Result $result
             */
            $result = $statement->execute();
            $id = $this->db->lastInsertRowId();

            $statement->close();
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
            return $this->error();
        }

        return $id;
    }    

    /**
     * @TODO - see if using PDO simplifies this (it probably does)
     * 
     * @param array<string, int|string> $params
     */
    public function list(array $params): SloganList|SloganError {
        $selectColumns = "rowid, timestamp, username, userid, slogan";
        $selectCount = "COUNT(1) AS _count";
        $selectBase = <<<SEL
            SELECT %select%
            FROM slogans
            %where%
            ORDER BY rowid DESC
            LIMIT :offset, :limit
            SEL;

        $where = "";

        $limit = intval($params[static::PARAM_PAGE_SIZE] ?? static::DEFAULT_PAGE_SIZE);
        $page = intval($params[static::PARAM_PAGE] ?? static::DEFAULT_PAGE);
        $offset = abs($page - 1) * $limit;

        $author = intval($params[static::PARAM_AUTHOR] ?? 0);
        if ($author) {
            $where = "WHERE userid = :userid";
        }
        
        try {
            // Query: slogans

            /**
             * @var \SQLite3Stmt $statement
             */
            $statement = $this->db->prepare(strtr($selectBase, [
                "%select%" => $selectColumns,
                "%where%" => $where
            ]));
            $statement->bindValue(":limit", $limit, SQLITE3_INTEGER);
            $statement->bindValue(":offset", $offset, SQLITE3_INTEGER);
            if($author) {
                $statement->bindValue(":userid", $author, SQLITE3_INTEGER);
            }

            /**
             * @var \SQLite3Result $result
             */
            $result = $statement->execute();

            $data = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $data[] = new Slogan(...$row);
            }

            $numResults = count($data);

            $statement->close();
    
            // Query: count

            /**
             * @var \SQLite3Stmt $countStatement
             */
            $countStatement = $this->db->prepare(strtr($selectBase, [
                "%select%" => $selectCount,
                "%where%" => $where
            ]));
            $countStatement->bindValue(":limit", -1, SQLITE3_INTEGER);
            $countStatement->bindValue(":offset", 0, SQLITE3_INTEGER);
            if ($author) {
                $countStatement->bindValue(":userid", $author, SQLITE3_INTEGER);
            }

            $queryCount = $this->db->querySingle($countStatement->getSQL(true));

            $countStatement->close();
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
            return $this->error();
        }

        $filter = [];
        if ($author) {
            $filter["author"] = $author;
        }

        return new SloganList($data, new ListMeta(
            page: $page,
            pageSize: $limit,
            results: $numResults,
            previousPage: ($page > 1 ? $page - 1 : null),
            nextPage: ($numResults == $limit && ($numResults * $page) < $queryCount ? $page + 1 : null),
            filter: $filter            
        ));
    }

    /**
     * @param int $rowId
     * @return Slogan
     */
    public function get(int $rowId): Slogan|SloganError {
        $select = <<<SEL
            SELECT rowid, timestamp, username, userid, slogan
            FROM slogans
            WHERE rowid = :id
            SEL;

        try {
            /**
             * @var \SQLite3Stmt $statement
             */
            $statement = $this->db->prepare($select);
            $statement->bindValue(":id", $rowId);

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
            return $this->error();
        }

        return new Slogan(...$data);
    }

    /**
     * @return Slogan
     */
    public function getRandom(): Slogan | SloganError {
        $select = <<<SEL
            SELECT rowid, timestamp, username, userid, slogan 
            FROM slogans ORDER BY RANDOM() LIMIT 1
            SEL;

        try {
            /**
             * @var array<string, mixed> $data
             */
            $data = $this->db->querySingle($select, true);
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
            return $this->error();
        }

        return new Slogan(...$data);
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
