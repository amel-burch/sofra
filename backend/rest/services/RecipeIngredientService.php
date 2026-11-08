<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/RecipeIngredientDao.php';
require_once __DIR__ . '/../dao/RecipeDao.php';
require_once __DIR__ . '/../dao/IngredientDao.php';

/** One row of a recipe's ingredient list (M:N recipes <-> ingredients). */
class RecipeIngredientService extends BaseService
{
    protected $entity_name = 'Recipe ingredient';
    protected $nullable = ['note'];
    /** A row always belongs to the same recipe; to move it, delete and add. */
    protected $immutable = ['recipe_id'];

    private $recipe_dao;
    private $ingredient_dao;

    public function __construct($dao = null, $recipe_dao = null, $ingredient_dao = null)
    {
        parent::__construct($dao ?? new RecipeIngredientDao());
        $this->recipe_dao = $recipe_dao ?? new RecipeDao();
        $this->ingredient_dao = $ingredient_dao ?? new IngredientDao();
    }

    public function get_by_recipe($recipe_id)
    {
        if (!$this->recipe_dao->get_by_id($recipe_id)) {
            throw new NotFoundException('Recipe not found.');
        }
        return $this->dao->get_by_recipe($recipe_id);
    }

    protected function validate($row, $id = null)
    {
        Validator::required($row, ['recipe_id', 'ingredient_id', 'quantity']);
        Validator::integer($row['recipe_id'], 'recipe_id', 1);
        Validator::integer($row['ingredient_id'], 'ingredient_id', 1);
        Validator::number($row['quantity'], 'quantity', 0.01, 100000);
        if (!empty($row['note'])) {
            Validator::length($row['note'], 'note', 0, 100);
        }
        $this->must_exist($this->recipe_dao, $row['recipe_id'], 'recipe_id', 'Recipe');
        $this->must_exist($this->ingredient_dao, $row['ingredient_id'], 'ingredient_id', 'Ingredient');

        // Rule: the same ingredient only once per recipe
        foreach ($this->dao->get_by_recipe($row['recipe_id']) as $other) {
            if ($other['ingredient_id'] == $row['ingredient_id'] && $other['id'] != $id) {
                throw new ValidationException('This ingredient is already in the recipe. Change its quantity instead.');
            }
        }
    }
}
