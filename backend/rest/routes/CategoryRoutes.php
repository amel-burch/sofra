<?php
/**
 * PRESENTATION LAYER - categories
 * Routes only read the request, call the service and send JSON. Errors (400/404/500) are
 * turned into JSON by Flight::map('error') in index.php.
 * OpenAPI documentation: public/v1/docs/paths/CategoryPaths.php
 */

Flight::route('GET /categories', function () {
    Flight::json(Flight::category_service()->get_all());
});

Flight::route('GET /categories/@id:[0-9]+', function ($id) {
    Flight::json(Flight::category_service()->get_by_id($id));
});

Flight::route('POST /categories', function () {
    Flight::json(Flight::category_service()->add(Flight::request()->data->getData()), 201);
});

Flight::route('PUT /categories/@id:[0-9]+', function ($id) {
    Flight::json(Flight::category_service()->update(Flight::request()->data->getData(), $id));
});

Flight::route('DELETE /categories/@id:[0-9]+', function ($id) {
    Flight::category_service()->delete($id);
    Flight::json(['message' => 'Category deleted.']);
});
