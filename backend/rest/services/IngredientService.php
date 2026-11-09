<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/IngredientDao.php';

class IngredientService extends BaseService
{
    const UNITS = ['g', 'ml', 'pcs', 'tbsp', 'tsp'];

    protected $entity_name = 'Ingredient';
    protected $nullable = ['calories_per_100'];

    public function __construct($dao = null)
    {
        parent::__construct($dao ?? new IngredientDao());
    }

    /** Autocomplete in the recipe form. */
    public function search($term)
    {
        $term = trim((string) $term);
        if (mb_strlen($term) < 1) {
            return $this->get_all();
        }
        return $this->dao->search_by_name($term);
    }

    /** Rule: an ingredient used in a recipe cannot be deleted (also enforced by ON DELETE RESTRICT). */
    public function delete($id)
    {
        $ingredient = $this->get_by_id($id);
        if ($ingredient['recipe_count'] > 0) {
            throw new ValidationException("This ingredient is used in {$ingredient['recipe_count']} recipe(s) and cannot be deleted.");
        }
        $this->dao->delete($id);
    }

    protected function validate($ingredient, $id = null)
    {
        Validator::required($ingredient, ['name', 'unit']);
        Validator::length($ingredient['name'], 'name', 2, 80);
        Validator::one_of($ingredient['unit'], 'unit', self::UNITS);
        if (isset($ingredient['calories_per_100'])) {
            Validator::integer($ingredient['calories_per_100'], 'calories_per_100', 0, 900);   // pure fat is ~900 kcal
        }

        // Rule: ingredient names are unique
        $existing = $this->dao->get_by_name($ingredient['name']);
        if ($existing && $existing['id'] != $id) {
            throw new ValidationException("An ingredient named '{$ingredient['name']}' already exists.");
        }
    }
}
