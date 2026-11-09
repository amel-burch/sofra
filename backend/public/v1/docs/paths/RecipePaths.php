<?php
use OpenApi\Attributes as OA;

/*
 * OpenAPI documentation of rest/routes/RecipeRoutes.php.
 * swagger-php reads the #[OA\...] attributes; the methods are empty - they only carry the documentation.
 * Keep it in sync with the routes: one method per endpoint.
 */
class RecipePaths
{
    #[OA\Get(
        path: '/recipes',
        tags: ['recipes'],
        summary: 'List all recipes',
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, description: 'Text in the title or description', example: 'pita', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'category_id', in: 'query', required: false, description: 'Only recipes of this category', example: 1, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'user_id', in: 'query', required: false, description: 'Only recipes of this author', example: 2, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of recipes', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Recipe'))),
            new OA\Response(response: 400, description: 'Invalid data or broken business rule', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getRecipes() {}

    #[OA\Get(
        path: '/recipes/{id}',
        tags: ['recipes'],
        summary: 'Get one recipe',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The recipe', content: new OA\JsonContent(ref: '#/components/schemas/RecipeDetails')),
            new OA\Response(response: 404, description: 'Recipe not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getRecipesById() {}

    #[OA\Post(
        path: '/recipes',
        tags: ['recipes'],
        summary: 'Create a recipe',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/RecipeInput')),
        responses: [
            new OA\Response(response: 201, description: 'Created recipe', content: new OA\JsonContent(ref: '#/components/schemas/RecipeDetails')),
            new OA\Response(response: 400, description: 'Invalid data or broken business rule', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postRecipes() {}

    #[OA\Put(
        path: '/recipes/{id}',
        tags: ['recipes'],
        summary: 'Update a recipe',
        description: 'Only the sent fields are changed; the rules are checked on the whole entity.',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/RecipeInput')),
        responses: [
            new OA\Response(response: 200, description: 'Updated recipe', content: new OA\JsonContent(ref: '#/components/schemas/RecipeDetails')),
            new OA\Response(response: 400, description: 'Invalid data or broken business rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Recipe not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putRecipesById() {}

    #[OA\Delete(
        path: '/recipes/{id}',
        tags: ['recipes'],
        summary: 'Delete a recipe',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, description: 'Recipe not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteRecipesById() {}

    #[OA\Get(
        path: '/recipes/top-rated',
        tags: ['recipes'],
        summary: 'Best rated recipes (dashboard)',
        parameters: [
            new OA\Parameter(name: 'limit', in: 'query', required: false, description: 'How many (1-20, default 5)', example: 5, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Recipes with reviews, best first', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Recipe'))),
            new OA\Response(response: 400, description: 'Invalid data or broken business rule', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getTopRatedRecipes() {}

    #[OA\Get(
        path: '/recipes/{id}/ingredients',
        tags: ['recipes'],
        summary: 'Ingredient list of a recipe',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Ingredients', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/RecipeIngredient'))),
            new OA\Response(response: 404, description: 'Recipe not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getRecipeIngredientsOfRecipe() {}

    #[OA\Get(
        path: '/recipes/{id}/reviews',
        tags: ['recipes'],
        summary: 'Reviews of a recipe',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Reviews', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Review'))),
            new OA\Response(response: 404, description: 'Recipe not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getReviewsOfRecipe() {}
}
