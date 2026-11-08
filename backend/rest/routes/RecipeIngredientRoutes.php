<?php
/**
 * PRESENTATION LAYER - recipe ingredients (M:N rows)
 * Routes only read the request, call the service and send JSON. Errors (400/404/500) are
 * turned into JSON by Flight::map('error') in index.php.
 * OpenAPI documentation: public/v1/docs/paths/RecipeIngredientPaths.php
 */

Flight::route('GET /recipe-ingredients', function () {
    Flight::json(Flight::recipe_ingredient_service()->get_all());
});

Flight::route('GET /recipe-ingredients/@id:[0-9]+', function ($id) {
    Flight::json(Flight::recipe_ingredient_service()->get_by_id($id));
});

Flight::route('POST /recipe-ingredients', function () {
    Flight::json(Flight::recipe_ingredient_service()->add(Flight::request()->data->getData()), 201);
});

Flight::route('PUT /recipe-ingredients/@id:[0-9]+', function ($id) {
    Flight::json(Flight::recipe_ingredient_service()->update(Flight::request()->data->getData(), $id));
});

Flight::route('DELETE /recipe-ingredients/@id:[0-9]+', function ($id) {
    Flight::recipe_ingredient_service()->delete($id);
    Flight::json(['message' => 'Recipe ingredient deleted.']);
});
