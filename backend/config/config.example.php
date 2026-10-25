<?php
/*
 * Configuration TEMPLATE.
 *
 * 1. Copy this file to config.php (same folder).
 * 2. Put your own values into config.php.
 *
 * config.php is in .gitignore and is never committed (DB password, JWT secret, API keys).
 */
class Config
{
    public static function DB_HOST()     { return self::get_env('DB_HOST', '127.0.0.1'); }
    public static function DB_PORT()     { return self::get_env('DB_PORT', 3306); }
    public static function DB_NAME()     { return self::get_env('DB_NAME', 'sofra'); }
    public static function DB_USER()     { return self::get_env('DB_USER', 'root'); }
    public static function DB_PASSWORD() { return self::get_env('DB_PASSWORD', ''); }

    // Used from milestone 4 (JWT)
    public static function JWT_SECRET()  { return self::get_env('JWT_SECRET', 'change-me'); }

    // TheMealDB (milestone 3). "1" is the public test key.
    public static function MEALDB_BASE_URL() { return self::get_env('MEALDB_BASE_URL', 'https://www.themealdb.com/api/json/v1/'); }
    public static function MEALDB_API_KEY()  { return self::get_env('MEALDB_API_KEY', '1'); }

    /**
     * Environment variables (set on the server) win over the default values above.
     */
    public static function get_env($name, $default)
    {
        $value = getenv($name);
        return ($value !== false && trim($value) !== '') ? $value : $default;
    }
}
