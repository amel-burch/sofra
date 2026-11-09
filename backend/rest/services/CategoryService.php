<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CategoryDao.php';

class CategoryService extends BaseService
{
    protected $entity_name = 'Category';
    protected $nullable = ['description'];

    public function __construct($dao = null)
    {
        parent::__construct($dao ?? new CategoryDao());
    }

    /** Rule: a category that still has recipes cannot be deleted (also enforced by ON DELETE RESTRICT). */
    public function delete($id)
    {
        $category = $this->get_by_id($id);
        if ($category['recipe_count'] > 0) {
            throw new ValidationException("This category still has {$category['recipe_count']} recipe(s). Move or delete them first.");
        }
        $this->dao->delete($id);
    }

    protected function validate($category, $id = null)
    {
        Validator::required($category, ['name']);
        Validator::length($category['name'], 'name', 2, 60);
        if (!empty($category['description'])) {
            Validator::length($category['description'], 'description', 0, 255);
        }

        // Rule: category names are unique
        $existing = $this->dao->get_by_name($category['name']);
        if ($existing && $existing['id'] != $id) {
            throw new ValidationException("A category named '{$category['name']}' already exists.");
        }
    }
}
