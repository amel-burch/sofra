<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/RecipeDao.php';
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/CategoryDao.php';
require_once __DIR__ . '/../dao/IngredientDao.php';
require_once __DIR__ . '/../dao/RecipeIngredientDao.php';

class RecipeService extends BaseService
{
    const DIFFICULTIES = ['easy', 'medium', 'hard'];

    protected $entity_name = 'Recipe';
    protected $nullable = ['description', 'image_url'];
    /** The author of a recipe never changes. */
    protected $immutable = ['user_id'];

    private $user_dao;
    private $category_dao;
    private $ingredient_dao;
    private $recipe_ingredient_dao;

    public function __construct($dao = null, $user_dao = null, $category_dao = null, $ingredient_dao = null, $recipe_ingredient_dao = null)
    {
        parent::__construct($dao ?? new RecipeDao());
        $this->user_dao = $user_dao ?? new UserDao();
        $this->category_dao = $category_dao ?? new CategoryDao();
        $this->ingredient_dao = $ingredient_dao ?? new IngredientDao();
        $this->recipe_ingredient_dao = $recipe_ingredient_dao ?? new RecipeIngredientDao();
    }

    /** Recipes page filters: ?search=pita, ?category_id=2, ?user_id=1 (only one is used, in that order). */
    public function get_filtered($filters)
    {
        if (!empty($filters['search'])) {
            return $this->dao->search(trim($filters['search']));
        }
        if (!empty($filters['category_id'])) {
            Validator::integer($filters['category_id'], 'category_id', 1);
            return $this->dao->get_by_category($filters['category_id']);
        }
        if (!empty($filters['user_id'])) {
            Validator::integer($filters['user_id'], 'user_id', 1);
            return $this->dao->get_by_user($filters['user_id']);
        }
        return $this->dao->get_all();
    }

    public function get_top_rated($limit)
    {
        Validator::integer($limit, 'limit', 1, 20);
        return $this->dao->get_top_rated($limit);
    }

    /** Recipe details page: the recipe + its ingredient list in one response. */
    public function get_details($id)
    {
        $recipe = $this->get_by_id($id);
        $recipe['ingredients'] = $this->recipe_ingredient_dao->get_by_recipe($id);
        return $recipe;
    }

    /**
     * Optional "ingredients": [{ingredient_id, quantity, note}] in the body.
     * The recipe and its ingredients are saved in ONE transaction - all or nothing.
     */
    public function add($recipe)
    {
        $recipe = $this->clean($recipe);
        $recipe['difficulty'] = $recipe['difficulty'] ?? 'medium';
        $this->validate($recipe);
        $ingredients = $this->validate_ingredients($recipe['ingredients'] ?? []);

        $this->dao->begin_transaction();
        try {
            $created = $this->dao->add($recipe);
            $this->save_ingredients($created['id'], $ingredients);
            $this->dao->commit();
        } catch (Throwable $e) {
            $this->dao->rollback();
            throw $e;
        }
        return $this->get_details($created['id']);
    }

    /** When "ingredients" is sent, the old list is replaced by the new one; otherwise it stays. */
    public function update($recipe, $id)
    {
        $recipe = $this->clean($recipe);
        $replace = array_key_exists('ingredients', $recipe);
        $ingredients = $replace ? $this->validate_ingredients($recipe['ingredients']) : [];

        $this->dao->begin_transaction();
        try {
            parent::update($recipe, $id);
            if ($replace) {
                $this->recipe_ingredient_dao->delete_by_recipe($id);
                $this->save_ingredients($id, $ingredients);
            }
            $this->dao->commit();
        } catch (Throwable $e) {
            $this->dao->rollback();
            throw $e;
        }
        return $this->get_details($id);
    }

    protected function validate($recipe, $id = null)
    {
        Validator::required($recipe, ['user_id', 'category_id', 'title', 'instructions']);
        Validator::integer($recipe['user_id'], 'user_id', 1);
        Validator::integer($recipe['category_id'], 'category_id', 1);
        Validator::length($recipe['title'], 'title', 3, 150);
        Validator::length($recipe['instructions'], 'instructions', 10, 10000);
        if (!empty($recipe['description'])) {
            Validator::length($recipe['description'], 'description', 0, 2000);
        }
        foreach (['prep_minutes', 'cook_minutes'] as $field) {
            if (isset($recipe[$field])) {
                Validator::integer($recipe[$field], $field, 0, 1440);   // max. one day
            }
        }
        if (isset($recipe['servings'])) {
            Validator::integer($recipe['servings'], 'servings', 1, 50);
        }
        if (isset($recipe['difficulty'])) {
            Validator::one_of($recipe['difficulty'], 'difficulty', self::DIFFICULTIES);
        }
        if (!empty($recipe['image_url'])) {
            Validator::url($recipe['image_url'], 'image_url');
            Validator::length($recipe['image_url'], 'image_url', 0, 255);
        }
        $this->must_exist($this->user_dao, $recipe['user_id'], 'user_id', 'User');
        $this->must_exist($this->category_dao, $recipe['category_id'], 'category_id', 'Category');
    }

    /** Every item needs an existing ingredient and a positive quantity; the same ingredient only once. */
    private function validate_ingredients($items)
    {
        if (!is_array($items)) {
            throw new ValidationException("Field 'ingredients' must be a list.");
        }
        $seen = [];
        foreach ($items as $i => $item) {
            $n = $i + 1;
            if (!is_array($item)) {
                throw new ValidationException("Ingredient #$n must be an object with ingredient_id and quantity.");
            }
            Validator::required($item, ['ingredient_id', 'quantity']);
            Validator::integer($item['ingredient_id'], "ingredients[$n].ingredient_id", 1);
            Validator::number($item['quantity'], "ingredients[$n].quantity", 0.01, 100000);
            if (!empty($item['note'])) {
                Validator::length($item['note'], "ingredients[$n].note", 0, 100);
            }
            $this->must_exist($this->ingredient_dao, $item['ingredient_id'], "ingredients[$n].ingredient_id", 'Ingredient');
            if (isset($seen[$item['ingredient_id']])) {
                throw new ValidationException("Ingredient #$n is already in the list.");
            }
            $seen[$item['ingredient_id']] = true;
        }
        return $items;
    }

    private function save_ingredients($recipe_id, $items)
    {
        foreach ($items as $item) {
            $this->recipe_ingredient_dao->add([
                'recipe_id' => $recipe_id,
                'ingredient_id' => $item['ingredient_id'],
                'quantity' => $item['quantity'],
                'note' => isset($item['note']) && trim($item['note']) !== '' ? trim($item['note']) : null
            ]);
        }
    }
}
