<?php
use OpenApi\Attributes as OA;

/*
 * OpenAPI documentation of rest/routes/FavoriteRoutes.php.
 * swagger-php reads the #[OA\...] attributes; the methods are empty - they only carry the documentation.
 * Keep it in sync with the routes: one method per endpoint.
 */
class FavoritePaths
{
    #[OA\Get(
        path: '/favorites',
        tags: ['favorites'],
        summary: 'List all favorites',
        responses: [
            new OA\Response(response: 200, description: 'List of favorites', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Favorite')))
        ]
    )]
    public function getFavorites() {}

    #[OA\Get(
        path: '/favorites/{id}',
        tags: ['favorites'],
        summary: 'Get one favorite',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The favorite', content: new OA\JsonContent(ref: '#/components/schemas/Favorite')),
            new OA\Response(response: 404, description: 'Favorite not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getFavoritesById() {}

    #[OA\Post(
        path: '/favorites',
        tags: ['favorites'],
        summary: 'Create a favorite',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/FavoriteInput')),
        responses: [
            new OA\Response(response: 201, description: 'Created favorite', content: new OA\JsonContent(ref: '#/components/schemas/Favorite')),
            new OA\Response(response: 400, description: 'Invalid data or broken business rule', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postFavorites() {}

    #[OA\Put(
        path: '/favorites/{id}',
        tags: ['favorites'],
        summary: 'Update a favorite',
        description: 'Only the sent fields are changed; the rules are checked on the whole entity.',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/FavoriteInput')),
        responses: [
            new OA\Response(response: 200, description: 'Updated favorite', content: new OA\JsonContent(ref: '#/components/schemas/Favorite')),
            new OA\Response(response: 400, description: 'Invalid data or broken business rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Favorite not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putFavoritesById() {}

    #[OA\Delete(
        path: '/favorites/{id}',
        tags: ['favorites'],
        summary: 'Delete a favorite',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, description: 'Favorite not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteFavoritesById() {}
}
