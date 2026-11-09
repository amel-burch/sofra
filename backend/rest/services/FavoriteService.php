<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/FavoriteDao.php';
require_once __DIR__ . '/../dao/RecipeDao.php';
require_once __DIR__ . '/../dao/UserDao.php';

/** A recipe saved by a user (heart button). */
class FavoriteService extends BaseService
{
    protected $entity_name = 'Favorite';
    /** A favorite always belongs to the same user. */
    protected $immutable = ['user_id'];

    private $recipe_dao;
    private $user_dao;

    public function __construct($dao = null, $recipe_dao = null, $user_dao = null)
    {
        parent::__construct($dao ?? new FavoriteDao());
        $this->recipe_dao = $recipe_dao ?? new RecipeDao();
        $this->user_dao = $user_dao ?? new UserDao();
    }

    public function get_by_user($user_id)
    {
        if (!$this->user_dao->get_by_id($user_id)) {
            throw new NotFoundException('User not found.');
        }
        return $this->dao->get_by_user($user_id);
    }

    protected function validate($favorite, $id = null)
    {
        Validator::required($favorite, ['user_id', 'recipe_id']);
        Validator::integer($favorite['user_id'], 'user_id', 1);
        Validator::integer($favorite['recipe_id'], 'recipe_id', 1);
        $this->must_exist($this->user_dao, $favorite['user_id'], 'user_id', 'User');
        $this->must_exist($this->recipe_dao, $favorite['recipe_id'], 'recipe_id', 'Recipe');

        // Rule: a recipe is saved at most once per user
        $existing = $this->dao->get_by_user_and_recipe($favorite['user_id'], $favorite['recipe_id']);
        if ($existing && $existing['id'] != $id) {
            throw new ValidationException('This recipe is already in your favorites.');
        }
    }
}
