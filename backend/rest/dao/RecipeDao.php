<?php
require_once __DIR__ . '/BaseDao.php';

class RecipeDao extends BaseDao
{
    /** Recipe columns + author/category names + rating summary (JOINs instead of extra requests). */
    private const SELECT = "
        SELECT r.*,
               CONCAT(u.first_name, ' ', u.last_name) AS author_name,
               c.name AS category_name,
               (SELECT ROUND(AVG(rv.rating), 1) FROM reviews rv WHERE rv.recipe_id = r.id) AS average_rating,
               (SELECT COUNT(*) FROM reviews rv WHERE rv.recipe_id = r.id) AS review_count
        FROM recipes r
        JOIN users u      ON u.id = r.user_id
        JOIN categories c ON c.id = r.category_id";

    public function __construct()
    {
        parent::__construct('recipes', [
            'user_id', 'category_id', 'title', 'description', 'instructions',
            'prep_minutes', 'cook_minutes', 'servings', 'difficulty', 'image_url'
        ]);
    }

    public function get_all()
    {
        return $this->query(self::SELECT . " ORDER BY r.created_at DESC, r.id DESC");
    }

    public function get_by_id($id)
    {
        return $this->query_unique(self::SELECT . " WHERE r.id = :id", ['id' => $id]);
    }

    public function get_by_user($user_id)
    {
        return $this->query(self::SELECT . " WHERE r.user_id = :user_id ORDER BY r.created_at DESC, r.id DESC", ['user_id' => $user_id]);
    }

    public function get_by_category($category_id)
    {
        return $this->query(self::SELECT . " WHERE r.category_id = :category_id ORDER BY r.title", ['category_id' => $category_id]);
    }

    /** Search box on the recipes page - matches the title or the description. */
    public function search($term)
    {
        return $this->query(
            self::SELECT . " WHERE r.title LIKE :term1 OR r.description LIKE :term2 ORDER BY r.title",
            ['term1' => '%' . $term . '%', 'term2' => '%' . $term . '%']
        );
    }

    /** Dashboard: best rated recipes first (only recipes that have reviews). */
    public function get_top_rated($limit = 5)
    {
        $stmt = self::connection()->prepare(self::SELECT . " HAVING review_count > 0 ORDER BY average_rating DESC, review_count DESC LIMIT :limit");
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
