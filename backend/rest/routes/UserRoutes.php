<?php
/**
 * PRESENTATION LAYER - users
 * Routes only read the request, call the service and send JSON. Errors (400/404/500) are
 * turned into JSON by Flight::map('error') in index.php.
 * OpenAPI documentation: public/v1/docs/paths/UserPaths.php
 */

Flight::route('GET /users', function () {
    Flight::json(Flight::user_service()->get_all());
});

Flight::route('GET /users/@id:[0-9]+', function ($id) {
    Flight::json(Flight::user_service()->get_by_id($id));
});

Flight::route('POST /users', function () {
    Flight::json(Flight::user_service()->add(Flight::request()->data->getData()), 201);
});

Flight::route('PUT /users/@id:[0-9]+', function ($id) {
    Flight::json(Flight::user_service()->update(Flight::request()->data->getData(), $id));
});

Flight::route('DELETE /users/@id:[0-9]+', function ($id) {
    Flight::user_service()->delete($id);
    Flight::json(['message' => 'User deleted.']);
});

Flight::route('GET /users/@id:[0-9]+/favorites', function ($id) {
    Flight::json(Flight::favorite_service()->get_by_user($id));
});
