<?php
/**
 * PRESENTATION LAYER - favorites
 * Routes only read the request, call the service and send JSON. Errors (400/404/500) are
 * turned into JSON by Flight::map('error') in index.php.
 * OpenAPI documentation: public/v1/docs/paths/FavoritePaths.php
 */

Flight::route('GET /favorites', function () {
    Flight::json(Flight::favorite_service()->get_all());
});

Flight::route('GET /favorites/@id:[0-9]+', function ($id) {
    Flight::json(Flight::favorite_service()->get_by_id($id));
});

Flight::route('POST /favorites', function () {
    Flight::json(Flight::favorite_service()->add(Flight::request()->data->getData()), 201);
});

Flight::route('PUT /favorites/@id:[0-9]+', function ($id) {
    Flight::json(Flight::favorite_service()->update(Flight::request()->data->getData(), $id));
});

Flight::route('DELETE /favorites/@id:[0-9]+', function ($id) {
    Flight::favorite_service()->delete($id);
    Flight::json(['message' => 'Favorite deleted.']);
});
