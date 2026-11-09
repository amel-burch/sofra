<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ReviewDao.php';
require_once __DIR__ . '/../dao/RecipeDao.php';
require_once __DIR__ . '/../dao/UserDao.php';

class ReviewService extends BaseService
{
    protected $entity_name = 'Review';
    protected $nullable = ['comment'];
    /** Only rating and comment can be edited - not who wrote it or for which recipe. */
    protected $immutable = ['user_id', 'recipe_id'];

    private $recipe_dao;
    private $user_dao;

    public function __construct($dao = null, $recipe_dao = null, $user_dao = null)
    {
        parent::__construct($dao ?? new ReviewDao());
        $this->recipe_dao = $recipe_dao ?? new RecipeDao();
        $this->user_dao = $user_dao ?? new UserDao();
    }

    public function get_by_recipe($recipe_id)
    {
        if (!$this->recipe_dao->get_by_id($recipe_id)) {
            throw new NotFoundException('Recipe not found.');
        }
        return $this->dao->get_by_recipe($recipe_id);
    }

    public function add($review)
    {
        $review = $this->clean($review);
        Validator::required($review, ['user_id', 'recipe_id']);
        Validator::integer($review['user_id'], 'user_id', 1);
        Validator::integer($review['recipe_id'], 'recipe_id', 1);
        $this->must_exist($this->user_dao, $review['user_id'], 'user_id', 'User');
        $recipe = $this->recipe_dao->get_by_id($review['recipe_id']);
        if (!$recipe) {
            throw new ValidationException("Recipe with id {$review['recipe_id']} does not exist (field 'recipe_id').");
        }

        // Rule: authors cannot rate their own recipes
        if ($recipe['user_id'] == $review['user_id']) {
            throw new ValidationException('You cannot review your own recipe.');
        }
        // Rule: one review per user per recipe
        if ($this->dao->get_by_user_and_recipe($review['user_id'], $review['recipe_id'])) {
            throw new ValidationException('You have already reviewed this recipe. Edit your review instead.');
        }
        return parent::add($review);
    }

    protected function validate($review, $id = null)
    {
        Validator::required($review, ['rating']);
        Validator::integer($review['rating'], 'rating', 1, 5);
        if (!empty($review['comment'])) {
            Validator::length($review['comment'], 'comment', 0, 1000);
        }
    }
}
