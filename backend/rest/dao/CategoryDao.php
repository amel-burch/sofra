<?php
require_once __DIR__ . '/BaseDao.php';

class CategoryDao extends BaseDao
{
    /** Category columns + how many recipes use it (shown in the admin table). */
    private const SELECT = "
        SELECT c.*,
               (SELECT COUNT(*) FROM recipes r WHERE r.category_id = c.id) AS recipe_count
        FROM categories c";

    public function __construct()
    {
        parent::__construct('categories', ['name', 'description']);
    }

    public function get_all()
    {
        return $this->query(self::SELECT . " ORDER BY c.name");
    }

    public function get_by_id($id)
    {
        return $this->query_unique(self::SELECT . " WHERE c.id = :id", ['id' => $id]);
    }

    /** Used to stop the same category name from being entered twice. */
    public function get_by_name($name)
    {
        return $this->query_unique("SELECT * FROM categories WHERE name = :name", ['name' => $name]);
    }
}
