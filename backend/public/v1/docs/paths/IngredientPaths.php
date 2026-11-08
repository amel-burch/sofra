<?php
use OpenApi\Attributes as OA;

/*
 * OpenAPI documentation of rest/routes/IngredientRoutes.php.
 * swagger-php reads the #[OA\...] attributes; the methods are empty - they only carry the documentation.
 * Keep it in sync with the routes: one method per endpoint.
 */
class IngredientPaths
{
    #[OA\Get(
        path: '/ingredients',
        tags: ['ingredients'],
        summary: 'List all ingredients',
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, description: 'Part of the name (autocomplete)', example: 'on', schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of ingredients', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Ingredient'))),
            new OA\Response(response: 400, description: 'Invalid data or broken business rule', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getIngredients() {}

    #[OA\Get(
        path: '/ingredients/{id}',
        tags: ['ingredients'],
        summary: 'Get one ingredient',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The ingredient', content: new OA\JsonContent(ref: '#/components/schemas/Ingredient')),
            new OA\Response(response: 404, description: 'Ingredient not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getIngredientsById() {}

    #[OA\Post(
        path: '/ingredients',
        tags: ['ingredients'],
        summary: 'Create a ingredient',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/IngredientInput')),
        responses: [
            new OA\Response(response: 201, description: 'Created ingredient', content: new OA\JsonContent(ref: '#/components/schemas/Ingredient')),
            new OA\Response(response: 400, description: 'Invalid data or broken business rule', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postIngredients() {}

    #[OA\Put(
        path: '/ingredients/{id}',
        tags: ['ingredients'],
        summary: 'Update a ingredient',
        description: 'Only the sent fields are changed; the rules are checked on the whole entity.',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/IngredientInput')),
        responses: [
            new OA\Response(response: 200, description: 'Updated ingredient', content: new OA\JsonContent(ref: '#/components/schemas/Ingredient')),
            new OA\Response(response: 400, description: 'Invalid data or broken business rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Ingredient not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putIngredientsById() {}

    #[OA\Delete(
        path: '/ingredients/{id}',
        tags: ['ingredients'],
        summary: 'Delete a ingredient',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 400, description: 'The ingredient is used in a recipe', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Ingredient not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteIngredientsById() {}
}
