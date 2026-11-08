<?php
use OpenApi\Attributes as OA;

/*
 * General API information + reusable schemas (referenced from the paths with ref: '#/components/schemas/...').
 * swagger-php 6 reads these PHP 8 attributes - the class itself is empty.
 * (Plain /* comments on purpose: swagger-php would turn a /** doc comment into a description.)
 */
#[OA\Info(
    title: 'Sofra API',
    description: 'REST API of Sofra - recipe sharing focused on Bosnian cuisine - IT 2001 Web Programming.',
    version: '1.0'
)]
#[OA\Server(url: API_BASE_URL, description: 'This server')]
#[OA\Schema(
    schema: 'Error',
    properties: [new OA\Property(property: 'error', type: 'string', example: 'Field \'title\' is required.')]
)]
#[OA\Schema(
    schema: 'Message',
    properties: [new OA\Property(property: 'message', type: 'string', example: 'Recipe deleted.')]
)]
#[OA\Schema(
    schema: 'UserInput',
    required: ['first_name', 'last_name', 'email'],
    properties: [
        new OA\Property(property: 'first_name', type: 'string', example: 'Lejla'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Begić'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'lejla.b@sofra.ba'),
        new OA\Property(
            property: 'password',
            type: 'string',
            format: 'password',
            example: 'Secret123',
            description: 'Required when creating (min. 8 characters, a letter and a number), optional when updating'
        ),
        new OA\Property(property: 'role', type: 'string', enum: ['admin', 'user'], example: 'user'),
        new OA\Property(property: 'city', type: 'string', nullable: true, example: 'Sarajevo')
    ]
)]
#[OA\Schema(
    schema: 'User',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 2),
        new OA\Property(property: 'first_name', type: 'string', example: 'Amra'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Hadžić'),
        new OA\Property(property: 'email', type: 'string', example: 'amra@sofra.ba'),
        new OA\Property(property: 'role', type: 'string', example: 'user'),
        new OA\Property(property: 'city', type: 'string', nullable: true, example: 'Mostar'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time')
    ]
)]
#[OA\Schema(
    schema: 'CategoryInput',
    required: ['name'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Soups'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Čorbe and broths')
    ]
)]
#[OA\Schema(
    schema: 'Category',
    allOf: [new OA\Schema(ref: '#/components/schemas/CategoryInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'recipe_count', type: 'integer', example: 2)
    ]
)]
#[OA\Schema(
    schema: 'IngredientInput',
    required: ['name', 'unit'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Paprika'),
        new OA\Property(property: 'unit', type: 'string', enum: ['g', 'ml', 'pcs', 'tbsp', 'tsp'], example: 'tsp'),
        new OA\Property(property: 'calories_per_100', type: 'integer', minimum: 0, maximum: 900, nullable: true, example: 282)
    ]
)]
#[OA\Schema(
    schema: 'Ingredient',
    allOf: [new OA\Schema(ref: '#/components/schemas/IngredientInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 3),
        new OA\Property(property: 'recipe_count', type: 'integer', example: 5)
    ]
)]
#[OA\Schema(
    schema: 'RecipeIngredientItem',
    required: ['ingredient_id', 'quantity'],
    properties: [
        new OA\Property(property: 'ingredient_id', type: 'integer', example: 14),
        new OA\Property(property: 'quantity', type: 'number', example: 300),
        new OA\Property(property: 'note', type: 'string', nullable: true, example: 'diced')
    ]
)]
#[OA\Schema(
    schema: 'RecipeInput',
    required: ['user_id', 'category_id', 'title', 'instructions'],
    properties: [
        new OA\Property(property: 'user_id', type: 'integer', example: 2, description: 'Author (cannot be changed later)'),
        new OA\Property(property: 'category_id', type: 'integer', example: 3),
        new OA\Property(property: 'title', type: 'string', example: 'Begova čorba'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'The bey\'s soup with chicken and okra.'),
        new OA\Property(property: 'instructions', type: 'string', example: 'Cook the meat, add the vegetables, thicken with egg yolk.'),
        new OA\Property(property: 'prep_minutes', type: 'integer', minimum: 0, maximum: 1440, example: 20),
        new OA\Property(property: 'cook_minutes', type: 'integer', minimum: 0, maximum: 1440, example: 60),
        new OA\Property(property: 'servings', type: 'integer', minimum: 1, maximum: 50, example: 4),
        new OA\Property(property: 'difficulty', type: 'string', enum: ['easy', 'medium', 'hard'], example: 'medium'),
        new OA\Property(property: 'image_url', type: 'string', format: 'uri', nullable: true, example: null),
        new OA\Property(
            property: 'ingredients',
            type: 'array',
            description: 'Optional. On update, sending this list replaces the old one; leaving it out keeps it.',
            items: new OA\Items(ref: '#/components/schemas/RecipeIngredientItem')
        )
    ]
)]
#[OA\Schema(
    schema: 'Recipe',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'category_id', type: 'integer', example: 1),
        new OA\Property(property: 'title', type: 'string', example: 'Burek'),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'instructions', type: 'string'),
        new OA\Property(property: 'prep_minutes', type: 'integer', example: 60),
        new OA\Property(property: 'cook_minutes', type: 'integer', example: 40),
        new OA\Property(property: 'servings', type: 'integer', example: 6),
        new OA\Property(property: 'difficulty', type: 'string', example: 'hard'),
        new OA\Property(property: 'image_url', type: 'string', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'author_name', type: 'string', example: 'Amel Karšić'),
        new OA\Property(property: 'category_name', type: 'string', example: 'Pita'),
        new OA\Property(property: 'average_rating', type: 'string', nullable: true, example: '4.5'),
        new OA\Property(property: 'review_count', type: 'integer', example: 2)
    ]
)]
#[OA\Schema(
    schema: 'RecipeDetails',
    allOf: [new OA\Schema(ref: '#/components/schemas/Recipe')],
    properties: [
        new OA\Property(property: 'ingredients', type: 'array', items: new OA\Items(ref: '#/components/schemas/RecipeIngredient'))
    ]
)]
#[OA\Schema(
    schema: 'RecipeIngredientInput',
    required: ['recipe_id', 'ingredient_id', 'quantity'],
    properties: [
        new OA\Property(property: 'recipe_id', type: 'integer', example: 1, description: 'Cannot be changed later'),
        new OA\Property(property: 'ingredient_id', type: 'integer', example: 17),
        new OA\Property(property: 'quantity', type: 'number', example: 1.5),
        new OA\Property(property: 'note', type: 'string', nullable: true, example: 'to taste')
    ]
)]
#[OA\Schema(
    schema: 'RecipeIngredient',
    allOf: [new OA\Schema(ref: '#/components/schemas/RecipeIngredientInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'ingredient_name', type: 'string', example: 'Flour'),
        new OA\Property(property: 'unit', type: 'string', example: 'g')
    ]
)]
#[OA\Schema(
    schema: 'ReviewInput',
    required: ['user_id', 'recipe_id', 'rating'],
    properties: [
        new OA\Property(property: 'user_id', type: 'integer', example: 3, description: 'Cannot be changed later'),
        new OA\Property(property: 'recipe_id', type: 'integer', example: 6, description: 'Cannot be changed later'),
        new OA\Property(property: 'rating', type: 'integer', minimum: 1, maximum: 5, example: 5),
        new OA\Property(property: 'comment', type: 'string', nullable: true, example: 'Just like my grandmother makes it.')
    ]
)]
#[OA\Schema(
    schema: 'Review',
    allOf: [new OA\Schema(ref: '#/components/schemas/ReviewInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'user_name', type: 'string', example: 'Emina Softić'),
        new OA\Property(property: 'recipe_title', type: 'string', example: 'Tufahije')
    ]
)]
#[OA\Schema(
    schema: 'FavoriteInput',
    required: ['user_id', 'recipe_id'],
    properties: [
        new OA\Property(property: 'user_id', type: 'integer', example: 3, description: 'Cannot be changed later'),
        new OA\Property(property: 'recipe_id', type: 'integer', example: 1)
    ]
)]
#[OA\Schema(
    schema: 'Favorite',
    allOf: [new OA\Schema(ref: '#/components/schemas/FavoriteInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'recipe_title', type: 'string', example: 'Burek'),
        new OA\Property(property: 'category_name', type: 'string', example: 'Pita')
    ]
)]
#[OA\Schema(
    schema: 'MealIngredient',
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Onion'),
        new OA\Property(property: 'measure', type: 'string', example: '2 chopped')
    ]
)]
#[OA\Schema(
    schema: 'Meal',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 53060),
        new OA\Property(property: 'name', type: 'string', example: 'Burek'),
        new OA\Property(property: 'category', type: 'string', example: 'Side'),
        new OA\Property(property: 'area', type: 'string', example: 'Croatian'),
        new OA\Property(property: 'instructions', type: 'string'),
        new OA\Property(property: 'thumbnail', type: 'string', format: 'uri'),
        new OA\Property(property: 'tags', type: 'array', items: new OA\Items(type: 'string')),
        new OA\Property(property: 'youtube', type: 'string', nullable: true),
        new OA\Property(property: 'source', type: 'string', nullable: true),
        new OA\Property(property: 'ingredients', type: 'array', items: new OA\Items(ref: '#/components/schemas/MealIngredient'))
    ]
)]
#[OA\Schema(
    schema: 'MealSummary',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 52959),
        new OA\Property(property: 'name', type: 'string', example: 'Baked salmon with fennel & tomatoes'),
        new OA\Property(property: 'thumbnail', type: 'string', format: 'uri')
    ]
)]
class OpenApiSpec {}
