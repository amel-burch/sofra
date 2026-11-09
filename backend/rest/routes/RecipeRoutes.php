<?php
/**
 * PRESENTATION LAYER - recipes
 * Routes only read the request, call the service and send JSON. Errors (400/404/500) are
 * turned into JSON by Flight::map('error') in index.php.
 * OpenAPI documentation: public/v1/docs/paths/RecipePaths.php
 */

Flight::route('GET /recipes', function () {
    // optional filters: ?search=pita | ?category_id=2 | ?user_id=1
    Flight::json(Flight::recipe_service()->get_filtered(Flight::request()->query->getData()));
});

// Dashboard: best rated recipes (only recipes with reviews)
Flight::route('GET /recipes/top-rated', function () {
    Flight::json(Flight::recipe_service()->get_top_rated(Flight::request()->query['limit'] ?? 5));
});

Flight::route('GET /recipes/@id:[0-9]+', function ($id) {
    // recipe details page: recipe + its ingredient list
    Flight::json(Flight::recipe_service()->get_details($id));
});

Flight::route('POST /recipes', function () {
    Flight::json(Flight::recipe_service()->add(Flight::request()->data->getData()), 201);
});

Flight::route('PUT /recipes/@id:[0-9]+', function ($id) {
    Flight::json(Flight::recipe_service()->update(Flight::request()->data->getData(), $id));
});

Flight::route('DELETE /recipes/@id:[0-9]+', function ($id) {
    Flight::recipe_service()->delete($id);
    Flight::json(['message' => 'Recipe deleted.']);
});

Flight::route('GET /recipes/@id:[0-9]+/ingredients', function ($id) {
    Flight::json(Flight::recipe_ingredient_service()->get_by_recipe($id));
});

Flight::route('GET /recipes/@id:[0-9]+/reviews', function ($id) {
    Flight::json(Flight::review_service()->get_by_recipe($id));
});
