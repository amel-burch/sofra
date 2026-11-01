<?php
require_once __DIR__ . '/BaseDao.php';

class FavoriteDao extends BaseDao
{
    /** Favorite + the saved recipe's title and category (favorites page cards). */
    private const SELECT = "
        SELECT f.*,
               r.title AS recipe_title,
               c.name  AS category_name
        FROM favorites f
        JOIN recipes r    ON r.id = f.recipe_id
        JOIN categories c ON c.id = r.category_id";

    public function __construct()
    {
        parent::__construct('favorites', ['user_id', 'recipe_id']);
    }

    public function get_all()
    {
        return $this->query(self::SELECT . " ORDER BY f.created_at DESC, f.id DESC");
    }

    public function get_by_id($id)
    {
        return $this->query_unique(self::SELECT . " WHERE f.id = :id", ['id' => $id]);
    }

    public function get_by_user($user_id)
    {
        return $this->query(self::SELECT . " WHERE f.user_id = :user_id ORDER BY f.created_at DESC, f.id DESC", ['user_id' => $user_id]);
    }

    /** A recipe can be saved only once - checked before insert (heart button toggles). */
    public function get_by_user_and_recipe($user_id, $recipe_id)
    {
        return $this->query_unique(
            "SELECT * FROM favorites WHERE user_id = :user_id AND recipe_id = :recipe_id",
            ['user_id' => $user_id, 'recipe_id' => $recipe_id]
        );
    }
}
