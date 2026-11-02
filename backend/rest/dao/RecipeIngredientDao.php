<?php
require_once __DIR__ . '/BaseDao.php';

class RecipeIngredientDao extends BaseDao
{
    /** Row of the M:N table + the ingredient name and unit. */
    private const SELECT = "
        SELECT ri.*,
               i.name AS ingredient_name,
               i.unit
        FROM recipe_ingredients ri
        JOIN ingredients i ON i.id = ri.ingredient_id";

    public function __construct()
    {
        parent::__construct('recipe_ingredients', ['recipe_id', 'ingredient_id', 'quantity', 'note']);
    }

    public function get_all()
    {
        return $this->query(self::SELECT . " ORDER BY ri.recipe_id, ri.id");
    }

    public function get_by_id($id)
    {
        return $this->query_unique(self::SELECT . " WHERE ri.id = :id", ['id' => $id]);
    }

    /** Ingredient list of one recipe (recipe details page). */
    public function get_by_recipe($recipe_id)
    {
        return $this->query(self::SELECT . " WHERE ri.recipe_id = :recipe_id ORDER BY ri.id", ['recipe_id' => $recipe_id]);
    }

    /** Used when the recipe form is saved: old list out, new list in (inside a transaction). */
    public function delete_by_recipe($recipe_id)
    {
        return $this->execute("DELETE FROM recipe_ingredients WHERE recipe_id = :recipe_id", ['recipe_id' => $recipe_id]);
    }
}
