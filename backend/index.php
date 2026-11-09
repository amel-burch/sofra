<?php
/*
 * Entry point of the REST API. Every request to /backend/... ends here (see .htaccess / server.php).
 *
 *   HTTP request -> routes (presentation) -> services (business logic) -> dao (data access) -> MySQL
 */
require __DIR__ . '/vendor/autoload.php';

if (!file_exists(__DIR__ . '/config/config.php')) {
    http_response_code(500);
    header('Content-Type: application/json');
    exit(json_encode(['error' => 'Missing backend/config/config.php - copy config.example.php to config.php.']));
}

require_once __DIR__ . '/rest/services/UserService.php';
require_once __DIR__ . '/rest/services/CategoryService.php';
require_once __DIR__ . '/rest/services/IngredientService.php';
require_once __DIR__ . '/rest/services/RecipeService.php';
require_once __DIR__ . '/rest/services/RecipeIngredientService.php';
require_once __DIR__ . '/rest/services/ReviewService.php';
require_once __DIR__ . '/rest/services/FavoriteService.php';
require_once __DIR__ . '/rest/services/MealDbService.php';

// Register services once, use them in every route: Flight::recipe_service()->...
Flight::register('user_service', 'UserService');
Flight::register('category_service', 'CategoryService');
Flight::register('ingredient_service', 'IngredientService');
Flight::register('recipe_service', 'RecipeService');
Flight::register('recipe_ingredient_service', 'RecipeIngredientService');
Flight::register('review_service', 'ReviewService');
Flight::register('favorite_service', 'FavoriteService');
Flight::register('mealdb_service', 'MealDbService');

/*
 * Broken JSON in the body would silently become an empty array ("Field 'x' is required").
 * Tell the client the real problem instead.
 */
Flight::before('start', function () {
    $body = Flight::request()->getBody();
    if (in_array(Flight::request()->method, ['POST', 'PUT', 'PATCH']) && trim($body) !== '') {
        json_decode($body);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ValidationException('The request body is not valid JSON.');
        }
    }
});

/*
 * ONE place that turns every exception into a JSON response.
 *  - HttpException (thrown by services) -> its own status code and message (400, 404, 503...)
 *  - duplicate / foreign key violation that slipped past the service rules -> 400
 *  - anything else (PDOException, bugs) -> 500 with a generic message.
 *    Never send stack traces, SQL or file paths to the client!
 */
Flight::map('error', function (Throwable $e) {
    if ($e instanceof HttpException) {
        $status = $e->get_status();
        $message = $e->getMessage();
    } elseif ($e instanceof PDOException && $e->getCode() === '23000') {
        $status = 400;
        $message = 'The data conflicts with existing records (duplicate or missing related entity).';
        error_log($e);
    } else {
        $status = 500;
        $message = 'Something went wrong on the server. Please try again later.';
        error_log($e);   // details only in the server log
    }
    Flight::response()
        ->clearBody()
        ->status($status)
        ->header('Content-Type', 'application/json')
        ->write(json_encode(['error' => $message]))
        ->send();
});

Flight::map('notFound', function () {
    Flight::response()
        ->clearBody()
        ->status(404)
        ->header('Content-Type', 'application/json')
        ->write(json_encode(['error' => 'Endpoint not found.']))
        ->send();
});

Flight::route('GET /', function () {
    Flight::json(['app' => 'Sofra API', 'message' => 'The backend is running.', 'docs' => 'public/v1/docs/']);
});

require_once __DIR__ . '/rest/routes/UserRoutes.php';
require_once __DIR__ . '/rest/routes/CategoryRoutes.php';
require_once __DIR__ . '/rest/routes/IngredientRoutes.php';
require_once __DIR__ . '/rest/routes/RecipeRoutes.php';
require_once __DIR__ . '/rest/routes/RecipeIngredientRoutes.php';
require_once __DIR__ . '/rest/routes/ReviewRoutes.php';
require_once __DIR__ . '/rest/routes/FavoriteRoutes.php';
require_once __DIR__ . '/rest/routes/WorldKitchenRoutes.php';

Flight::start();
