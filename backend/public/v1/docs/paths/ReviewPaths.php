<?php
use OpenApi\Attributes as OA;

/*
 * OpenAPI documentation of rest/routes/ReviewRoutes.php.
 * swagger-php reads the #[OA\...] attributes; the methods are empty - they only carry the documentation.
 * Keep it in sync with the routes: one method per endpoint.
 */
class ReviewPaths
{
    #[OA\Get(
        path: '/reviews',
        tags: ['reviews'],
        summary: 'List all reviews',
        responses: [
            new OA\Response(response: 200, description: 'List of reviews', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Review')))
        ]
    )]
    public function getReviews() {}

    #[OA\Get(
        path: '/reviews/{id}',
        tags: ['reviews'],
        summary: 'Get one review',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The review', content: new OA\JsonContent(ref: '#/components/schemas/Review')),
            new OA\Response(response: 404, description: 'Review not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getReviewsById() {}

    #[OA\Post(
        path: '/reviews',
        tags: ['reviews'],
        summary: 'Create a review',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ReviewInput')),
        responses: [
            new OA\Response(response: 201, description: 'Created review', content: new OA\JsonContent(ref: '#/components/schemas/Review')),
            new OA\Response(response: 400, description: 'Invalid data or broken business rule', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postReviews() {}

    #[OA\Put(
        path: '/reviews/{id}',
        tags: ['reviews'],
        summary: 'Update a review',
        description: 'Only the sent fields are changed; the rules are checked on the whole entity.',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ReviewInput')),
        responses: [
            new OA\Response(response: 200, description: 'Updated review', content: new OA\JsonContent(ref: '#/components/schemas/Review')),
            new OA\Response(response: 400, description: 'Invalid data or broken business rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Review not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putReviewsById() {}

    #[OA\Delete(
        path: '/reviews/{id}',
        tags: ['reviews'],
        summary: 'Delete a review',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, description: 'Review not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteReviewsById() {}
}
