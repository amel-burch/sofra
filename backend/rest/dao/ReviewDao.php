<?php
require_once __DIR__ . '/BaseDao.php';

class ReviewDao extends BaseDao
{
    private const SELECT = "
        SELECT rv.*,
               CONCAT(u.first_name, ' ', u.last_name) AS user_name,
               r.title AS recipe_title
        FROM reviews rv
        JOIN users u   ON u.id = rv.user_id
        JOIN recipes r ON r.id = rv.recipe_id";

    public function __construct()
    {
        parent::__construct('reviews', ['user_id', 'recipe_id', 'rating', 'comment']);
    }

    public function get_all()
    {
        return $this->query(self::SELECT . " ORDER BY rv.created_at DESC, rv.id DESC");
    }

    public function get_by_id($id)
    {
        return $this->query_unique(self::SELECT . " WHERE rv.id = :id", ['id' => $id]);
    }

    public function get_by_recipe($recipe_id)
    {
        return $this->query(self::SELECT . " WHERE rv.recipe_id = :recipe_id ORDER BY rv.created_at DESC, rv.id DESC", ['recipe_id' => $recipe_id]);
    }

    /** One review per user per recipe - checked before insert. */
    public function get_by_user_and_recipe($user_id, $recipe_id)
    {
        return $this->query_unique(
            "SELECT * FROM reviews WHERE user_id = :user_id AND recipe_id = :recipe_id",
            ['user_id' => $user_id, 'recipe_id' => $recipe_id]
        );
    }
}
