<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Pickleball Backend API',
    description: 'OpenAPI documentation for the API and settings controllers in the Pickleball backend.',
)]
#[OA\Server(
    url: '/',
    description: 'Application root',
)]
#[OA\Tag(
    name: 'API Auth',
    description: 'Sanctum authentication endpoints for mobile and API clients.',
)]
#[OA\Tag(
    name: 'API User',
    description: 'Authenticated API user endpoints.',
)]
#[OA\Tag(
    name: 'API Player',
    description: 'Authenticated player directory endpoints.',
)]
#[OA\Tag(
    name: 'API Match',
    description: 'Match lifecycle endpoints.',
)]
#[OA\Tag(
    name: 'API Game Type',
    description: 'Game type dictionary endpoints.',
)]
#[OA\Tag(
    name: 'Settings Profile',
    description: 'Browser-based profile settings endpoints.',
)]
#[OA\Tag(
    name: 'Settings Security',
    description: 'Browser-based security settings endpoints.',
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctumBearer',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Bearer',
    description: 'Use the bearer token issued by the login endpoint.',
)]
#[OA\SecurityScheme(
    securityScheme: 'laravelSession',
    type: 'apiKey',
    in: 'cookie',
    name: 'laravel_session',
    description: 'Authenticated browser session cookie used by web settings routes.',
)]
class OpenApiSpec
{
}

#[OA\Schema(
    schema: 'MessageResponse',
    required: ['message'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
    ],
    type: 'object',
)]
class MessageResponseSchema
{
}

#[OA\Schema(
    schema: 'ValidationErrorResponse',
    required: ['message', 'errors'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'The given data was invalid.'),
        new OA\Property(
            property: 'errors',
            additionalProperties: new OA\AdditionalProperties(
                type: 'array',
                items: new OA\Items(type: 'string'),
            ),
            type: 'object',
        ),
    ],
    type: 'object',
)]
class ValidationErrorResponseSchema
{
}
