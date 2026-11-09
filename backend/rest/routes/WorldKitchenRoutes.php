<?php
/**
 * PRESENTATION LAYER - external API (TheMealDB), "World kitchen" page.
 * The frontend never calls TheMealDB directly: our backend does it, so we control
 * timeouts, error handling, logging and the shape of the data.
 * OpenAPI documentation: public/v1/docs/paths/WorldKitchenPaths.php
 */

Flight::route('GET /world/search', function () {
    Flight::json(Flight::mealdb_service()->search(Flight::request()->query['q'] ?? ''));
});

Flight::route('GET /world/categories', function () {
    Flight::json(Flight::mealdb_service()->categories());
});

Flight::route('GET /world/categories/@category', function ($category) {
    Flight::json(Flight::mealdb_service()->by_category(urldecode($category)));
});

Flight::route('GET /world/meals/@id:[0-9]+', function ($id) {
    Flight::json(Flight::mealdb_service()->get_by_id($id));
});

Flight::route('GET /world/random', function () {
    Flight::json(Flight::mealdb_service()->random());
});
