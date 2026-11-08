<?php
use OpenApi\Attributes as OA;

/*
 * OpenAPI documentation of rest/routes/RecipeIngredientRoutes.php.
 * swagger-php reads the #[OA\...] attributes; the methods are empty - they only carry the documentation.
 * Keep it in sync with the routes: one method per endpoint.
 */
class RecipeIngredientPaths
{
    #[OA\Get(
        path: '/recipe-ingredients',
        tags: ['recipe-ingredients'],
        summary: 'List all recipe ingredients',
        responses: [
            new OA\Response(response: 200, description: 'List of recipe ingredients', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/RecipeIngredient')))
        ]
    )]
    public function getRecipeIngredients() {}

    #[OA\Get(
        path: '/recipe-ingredients/{id}',
        tags: ['recipe-ingredients'],
        summary: 'Get one recipe ingredient',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The recipe ingredient', content: new OA\JsonContent(ref: '#/components/schemas/RecipeIngredient')),
            new OA\Response(response: 404, description: 'Recipe ingredient not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getRecipeIngredientsById() {}

    #[OA\Post(
        path: '/recipe-ingredients',
        tags: ['recipe-ingredients'],
        summary: 'Create a recipe ingredient',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/RecipeIngredientInput')),
        responses: [
            new OA\Response(response: 201, description: 'Created recipe ingredient', content: new OA\JsonContent(ref: '#/components/schemas/RecipeIngredient')),
            new OA\Response(response: 400, description: 'Invalid data or broken business rule', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postRecipeIngredients() {}

    #[OA\Put(
        path: '/recipe-ingredients/{id}',
        tags: ['recipe-ingredients'],
        summary: 'Update a recipe ingredient',
        description: 'Only the sent fields are changed; the rules are checked on the whole entity.',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/RecipeIngredientInput')),
        responses: [
            new OA\Response(response: 200, description: 'Updated recipe ingredient', content: new OA\JsonContent(ref: '#/components/schemas/RecipeIngredient')),
            new OA\Response(response: 400, description: 'Invalid data or broken business rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Recipe ingredient not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putRecipeIngredientsById() {}

    #[OA\Delete(
        path: '/recipe-ingredients/{id}',
        tags: ['recipe-ingredients'],
        summary: 'Delete a recipe ingredient',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, description: 'Recipe ingredient not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteRecipeIngredientsById() {}
}
