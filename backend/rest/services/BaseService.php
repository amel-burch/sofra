<?php
require_once __DIR__ . '/../../utils/exceptions.php';
require_once __DIR__ . '/../../utils/Validator.php';

/**
 * BaseService - common business logic for every entity.
 *
 * Problems are signalled with EXCEPTIONS (utils/exceptions.php) that carry an HTTP status code;
 * the error handler in index.php turns them into JSON.
 *
 * Child services override validate() with their own rules and any CRUD method that needs
 * special behaviour. Services are the ONLY layer that calls the DAOs.
 */
class BaseService
{
    protected $dao;
    /** Used in error messages, e.g. "Recipe not found." */
    protected $entity_name = 'Entity';
    /** Optional text columns: "" is saved as NULL. */
    protected $nullable = [];
    /** Columns that cannot be changed after the row is created (e.g. who wrote a review). */
    protected $immutable = [];

    public function __construct($dao)
    {
        $this->dao = $dao;
    }

    public function get_all()
    {
        return $this->dao->get_all();
    }

    public function get_by_id($id)
    {
        $entity = $this->dao->get_by_id($id);
        if (!$entity) {
            throw new NotFoundException("{$this->entity_name} not found.");
        }
        return $entity;
    }

    public function add($entity)
    {
        $entity = $this->clean($entity);
        $this->validate($entity);
        $created = $this->dao->add($entity);
        return $this->get_by_id($created['id']);
    }

    /**
     * Works as PUT and PATCH: only the sent fields change, but the rules are checked
     * on the whole entity (existing values + sent values).
     */
    public function update($entity, $id)
    {
        $existing = $this->get_by_id($id);              // 404 if it does not exist
        $entity = array_diff_key($this->clean($entity), array_flip($this->immutable));
        $this->validate(array_merge($existing, $entity), $id);
        $this->dao->update($entity, $id);
        return $this->get_by_id($id);
    }

    public function delete($id)
    {
        $this->get_by_id($id);
        $this->dao->delete($id);
    }

    /**
     * Throw a ValidationException when $entity breaks a rule.
     * $id is null when creating and the entity id when updating.
     */
    protected function validate($entity, $id = null)
    {
    }

    /** Trim strings and turn "" into NULL for optional columns. */
    protected function clean($entity)
    {
        if (!is_array($entity)) {
            throw new ValidationException('The request body must be a JSON object.');
        }
        foreach ($entity as $key => $value) {
            if (is_string($value)) {
                $entity[$key] = trim($value);
                if ($entity[$key] === '' && in_array($key, $this->nullable, true)) {
                    $entity[$key] = null;
                }
            }
        }
        return $entity;
    }

    /** 400 (not 404) when a foreign key in the body points to nothing - the request data is wrong. */
    protected function must_exist($dao, $id, $field, $name)
    {
        if (!$dao->get_by_id($id)) {
            throw new ValidationException("$name with id $id does not exist (field '$field').");
        }
    }
}
