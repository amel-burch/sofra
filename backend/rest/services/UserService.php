<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/UserDao.php';

class UserService extends BaseService
{
    const ROLES = ['admin', 'user'];

    protected $entity_name = 'User';
    protected $nullable = ['city'];

    public function __construct($dao = null)
    {
        parent::__construct($dao ?? new UserDao());
    }

    /** Password is required for a new user and saved only as a bcrypt hash. */
    public function add($user)
    {
        $user = $this->clean($user);
        $user['role'] = $user['role'] ?? 'user';
        Validator::required($user, ['password']);
        Validator::password($user['password']);
        $this->validate($user);
        $user['password'] = password_hash($user['password'], PASSWORD_BCRYPT);
        $created = $this->dao->add($user);
        return $this->get_by_id($created['id']);
    }

    /** Password is optional when updating - empty means "keep the old one". */
    public function update($user, $id)
    {
        $user = $this->clean($user);
        if (!isset($user['password']) || $user['password'] === '') {
            unset($user['password']);
        } else {
            Validator::password($user['password']);
            $user['password'] = password_hash($user['password'], PASSWORD_BCRYPT);
        }

        // Rule: the last admin cannot become a normal user (nobody could manage the app anymore)
        $existing = $this->get_by_id($id);
        if ($existing['role'] === 'admin' && ($user['role'] ?? 'admin') !== 'admin' && $this->dao->count_by_role('admin') <= 1) {
            throw new ValidationException('This is the last admin account, its role cannot be changed.');
        }
        return parent::update($user, $id);
    }

    /** Rule: the last admin cannot be deleted. Deleting a user also deletes their recipes, reviews and favorites (ON DELETE CASCADE). */
    public function delete($id)
    {
        $user = $this->get_by_id($id);
        if ($user['role'] === 'admin' && $this->dao->count_by_role('admin') <= 1) {
            throw new ValidationException('This is the last admin account, it cannot be deleted.');
        }
        $this->dao->delete($id);
    }

    protected function validate($user, $id = null)
    {
        Validator::required($user, ['first_name', 'last_name', 'email', 'role']);
        Validator::length($user['first_name'], 'first_name', 2, 50);
        Validator::length($user['last_name'], 'last_name', 2, 50);
        Validator::email($user['email']);
        Validator::length($user['email'], 'email', 5, 100);
        Validator::one_of($user['role'], 'role', self::ROLES);
        if (!empty($user['city'])) {
            Validator::length($user['city'], 'city', 2, 60);
        }

        // Rule: one account per email address
        $existing = $this->dao->get_by_email($user['email']);
        if ($existing && $existing['id'] != $id) {
            throw new ValidationException('An account with this email address already exists.');
        }
    }
}
