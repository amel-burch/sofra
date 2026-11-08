<?php
require_once __DIR__ . '/exceptions.php';

/**
 * Small reusable validation helpers for services.
 * Every method throws a ValidationException (HTTP 400) with a readable message.
 */
class Validator
{
    /** Every field in $fields must exist and must not be empty. */
    public static function required($data, $fields)
    {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || (is_scalar($data[$field]) && trim((string) $data[$field]) === '')) {
                throw new ValidationException("Field '$field' is required.");
            }
        }
    }

    public static function length($value, $field, $min, $max)
    {
        $length = mb_strlen(trim((string) $value));
        if ($length < $min || $length > $max) {
            throw new ValidationException("Field '$field' must be between $min and $max characters long.");
        }
    }

    public static function email($value, $field = 'email')
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException("Field '$field' must be a valid email address.");
        }
    }

    public static function integer($value, $field, $min = null, $max = null)
    {
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            throw new ValidationException("Field '$field' must be a whole number.");
        }
        self::range($value, $field, $min, $max);
    }

    /** Decimal number, e.g. a quantity of 0.5 */
    public static function number($value, $field, $min = null, $max = null)
    {
        if (filter_var($value, FILTER_VALIDATE_FLOAT) === false) {
            throw new ValidationException("Field '$field' must be a number.");
        }
        self::range($value, $field, $min, $max);
    }

    public static function one_of($value, $field, $allowed)
    {
        if (!in_array($value, $allowed, true)) {
            throw new ValidationException("Field '$field' must be one of: " . implode(', ', $allowed) . ".");
        }
    }

    /** Only http(s) links - "javascript:..." must never end up in an <img src>. */
    public static function url($value, $field)
    {
        if (!filter_var($value, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $value)) {
            throw new ValidationException("Field '$field' must be a valid http(s) URL.");
        }
    }

    /** At least 8 characters, at least one letter and one digit. */
    public static function password($value, $field = 'password')
    {
        if (mb_strlen((string) $value) < 8 || !preg_match('/[A-Za-z]/', $value) || !preg_match('/\d/', $value)) {
            throw new ValidationException("Field '$field' must have at least 8 characters, including a letter and a number.");
        }
    }

    private static function range($value, $field, $min, $max)
    {
        if ($min !== null && $value < $min) {
            throw new ValidationException("Field '$field' must be at least $min.");
        }
        if ($max !== null && $value > $max) {
            throw new ValidationException("Field '$field' must be at most $max.");
        }
    }
}
