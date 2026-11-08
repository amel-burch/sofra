<?php
/**
 * PRESENTATION LAYER - ingredients
 * Routes only read the request, call the service and send JSON. Errors (400/404/500) are
 * turned into JSON by Flight::map('error') in index.php.
 * OpenAPI documentation: public/v1/docs/paths/IngredientPaths.php
 */

Flight::route('GET /ingredients', function () {
    // ?search=on -> autocomplete in the recipe form
    Flight::json(Flight::ingredient_service()->search(Flight::request()->query['search'] ?? ''));
});

Flight::route('GET /ingredients/@id:[0-9]+', function ($id) {
    Flight::json(Flight::ingredient_service()->get_by_id($id));
});

Flight::route('POST /ingredients', function () {
    Flight::json(Flight::ingredient_service()->add(Flight::request()->data->getData()), 201);
});

Flight::route('PUT /ingredients/@id:[0-9]+', function ($id) {
    Flight::json(Flight::ingredient_service()->update(Flight::request()->data->getData(), $id));
});

Flight::route('DELETE /ingredients/@id:[0-9]+', function ($id) {
    Flight::ingredient_service()->delete($id);
    Flight::json(['message' => 'Ingredient deleted.']);
});
