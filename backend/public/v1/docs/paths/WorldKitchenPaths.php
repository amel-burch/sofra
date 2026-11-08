<?php
use OpenApi\Attributes as OA;

/*
 * OpenAPI documentation of rest/routes/WorldKitchenRoutes.php.
 * swagger-php reads the #[OA\...] attributes; the methods are empty - they only carry the documentation.
 * Keep it in sync with the routes: one method per endpoint.
 */
class WorldKitchenPaths
{
    #[OA\Get(
        path: '/world/search',
        tags: ['world kitchen (TheMealDB)'],
        summary: 'Search international meals by name',
        parameters: [
            new OA\Parameter(name: 'q', in: 'query', required: true, description: 'Meal name, min. 2 characters', example: 'burek', schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Matching meals (empty list when nothing is found)', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Meal'))),
            new OA\Response(response: 400, description: 'Search text too short', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 503, description: 'TheMealDB is not available (timeout or error)', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getWorldSearch() {}

    #[OA\Get(
        path: '/world/categories',
        tags: ['world kitchen (TheMealDB)'],
        summary: 'Meal categories',
        responses: [
            new OA\Response(response: 200, description: 'Category names', content: new OA\JsonContent(type: 'array', items: new OA\Items(type: 'string'))),
            new OA\Response(response: 503, description: 'TheMealDB is not available (timeout or error)', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getWorldCategories() {}

    #[OA\Get(
        path: '/world/categories/{category}',
        tags: ['world kitchen (TheMealDB)'],
        summary: 'Meals of one category',
        parameters: [
            new OA\Parameter(name: 'category', in: 'path', required: true, example: 'Seafood', schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Meals (id, name, picture)', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/MealSummary'))),
            new OA\Response(response: 404, description: 'No meals in this category', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 503, description: 'TheMealDB is not available (timeout or error)', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getWorldMealsByCategory() {}

    #[OA\Get(
        path: '/world/meals/{id}',
        tags: ['world kitchen (TheMealDB)'],
        summary: 'Full details of one meal',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 53060, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The meal', content: new OA\JsonContent(ref: '#/components/schemas/Meal')),
            new OA\Response(response: 404, description: 'Meal not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 503, description: 'TheMealDB is not available (timeout or error)', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getWorldMealById() {}

    #[OA\Get(
        path: '/world/random',
        tags: ['world kitchen (TheMealDB)'],
        summary: 'A random meal ("Surprise me")',
        responses: [
            new OA\Response(response: 200, description: 'A random meal', content: new OA\JsonContent(ref: '#/components/schemas/Meal')),
            new OA\Response(response: 503, description: 'TheMealDB is not available (timeout or error)', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getWorldRandom() {}
}
