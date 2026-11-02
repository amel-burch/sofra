<?php
require_once __DIR__ . '/../../config/config.php';

/**
 * BaseDao - reusable CRUD for every table.
 *
 * Child classes only say WHICH table they use and WHICH columns may be written:
 *
 *   class CategoryDao extends BaseDao {
 *       public function __construct() { parent::__construct('categories', ['name', 'description']); }
 *   }
 *
 * Every query uses prepared statements. Table and column names cannot be bound as parameters,
 * so they come only from the child class and the column whitelist - never from the request.
 */
class BaseDao
{
    /** One PDO connection shared by all DAOs (created on first use). */
    private static $connection = null;

    protected $table_name;
    /** Columns that may be inserted/updated - every other key is ignored. */
    protected $columns;

    public function __construct($table_name, $columns = [])
    {
        $this->table_name = $table_name;
        $this->columns = $columns;
    }

    public static function connection()
    {
        if (self::$connection === null) {
            self::$connection = new PDO(
                "mysql:host=" . Config::DB_HOST() . ";port=" . Config::DB_PORT() . ";dbname=" . Config::DB_NAME() . ";charset=utf8mb4",
                Config::DB_USER(),
                Config::DB_PASSWORD(),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    // UPDATE returns matched rows, not changed rows - saving a form without changes is not "0 rows"
                    // (Pdo\Mysql::ATTR_FOUND_ROWS exists since PHP 8.4; the old PDO:: constant is deprecated in 8.5)
                    (class_exists('Pdo\Mysql') ? Pdo\Mysql::ATTR_FOUND_ROWS : PDO::MYSQL_ATTR_FOUND_ROWS) => true
                ]
            );
        }
        return self::$connection;
    }

    /* ---------- helpers for child classes ---------- */

    /** Run a SELECT and return all rows. */
    protected function query($query, $params = [])
    {
        $stmt = self::connection()->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Run a SELECT and return the first row (or null). */
    protected function query_unique($query, $params = [])
    {
        $results = $this->query($query, $params);
        return $results[0] ?? null;
    }

    /** Run INSERT/UPDATE/DELETE and return the number of affected rows. */
    protected function execute($query, $params = [])
    {
        $stmt = self::connection()->prepare($query);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /* ---------- CRUD ---------- */

    public function get_all()
    {
        return $this->query("SELECT * FROM {$this->table_name} ORDER BY id");
    }

    public function get_by_id($id)
    {
        return $this->query_unique("SELECT * FROM {$this->table_name} WHERE id = :id", ['id' => $id]);
    }

    public function count_all()
    {
        return (int) $this->query_unique("SELECT COUNT(*) AS total FROM {$this->table_name}")['total'];
    }

    /**
     * INSERT INTO recipes (title, servings) VALUES (:title, :servings)
     * Returns the saved entity with its new id.
     */
    public function add($entity)
    {
        $entity = $this->only_columns($entity);
        $columns = array_keys($entity);

        $query = "INSERT INTO {$this->table_name} (" . implode(', ', $columns) . ")"
               . " VALUES (:" . implode(', :', $columns) . ")";
        $this->execute($query, $entity);

        $entity['id'] = (int) self::connection()->lastInsertId();
        return $entity;
    }

    /**
     * UPDATE recipes SET title = :title, servings = :servings WHERE id = :id
     * Only the given columns change (works for both PUT and PATCH). Returns affected rows.
     */
    public function update($entity, $id)
    {
        $entity = $this->only_columns($entity);
        if (count($entity) === 0) {
            return 0;
        }

        $set = [];
        foreach (array_keys($entity) as $column) {
            $set[] = "$column = :$column";
        }
        $query = "UPDATE {$this->table_name} SET " . implode(', ', $set) . " WHERE id = :id";

        $entity['id'] = $id;
        return $this->execute($query, $entity);
    }

    public function delete($id)
    {
        return $this->execute("DELETE FROM {$this->table_name} WHERE id = :id", ['id' => $id]);
    }

    /* ---------- transactions (a recipe and its ingredients are saved together) ---------- */

    public function begin_transaction() { self::connection()->beginTransaction(); }
    public function commit()            { self::connection()->commit(); }
    public function rollback()          { if (self::connection()->inTransaction()) self::connection()->rollBack(); }

    /** Keep only keys that are real, writable columns of this table. */
    protected function only_columns($entity)
    {
        return array_intersect_key($entity, array_flip($this->columns));
    }
}
