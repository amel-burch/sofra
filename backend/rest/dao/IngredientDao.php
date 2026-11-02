<?php
require_once __DIR__ . '/BaseDao.php';

class IngredientDao extends BaseDao
{
    /** Ingredient columns + in how many recipes it is used. */
    private const SELECT = "
        SELECT i.*,
               (SELECT COUNT(*) FROM recipe_ingredients ri WHERE ri.ingredient_id = i.id) AS recipe_count
        FROM ingredients i";

    public function __construct()
    {
        parent::__construct('ingredients', ['name', 'unit', 'calories_per_100']);
    }

    public function get_all()
    {
        return $this->query(self::SELECT . " ORDER BY i.name");
    }

    public function get_by_id($id)
    {
        return $this->query_unique(self::SELECT . " WHERE i.id = :id", ['id' => $id]);
    }

    public function get_by_name($name)
    {
        return $this->query_unique("SELECT * FROM ingredients WHERE name = :name", ['name' => $name]);
    }

    /** Autocomplete in the recipe form: "on" -> Onion. */
    public function search_by_name($term)
    {
        return $this->query("SELECT * FROM ingredients WHERE name LIKE :term ORDER BY name", ['term' => '%' . $term . '%']);
    }
}
