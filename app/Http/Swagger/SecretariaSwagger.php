<?php

namespace App\Http\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'SecretariaItem',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'nombre', type: 'string', example: 'Secretaría de Obras Públicas'),
        new OA\Property(property: 'sigla', type: 'string', nullable: true, example: 'SEOP'),
        new OA\Property(property: 'slug', type: 'string', example: 'secretaria-de-obras-publicas'),
        new OA\Property(property: 'direccion_fisica', type: 'string', nullable: true),
        new OA\Property(property: 'telefono', type: 'string', nullable: true, example: '2123456'),
        new OA\Property(property: 'email', type: 'string', nullable: true, example: 'seop@cenefco.gob.bo'),
        new OA\Property(property: 'horario_atencion', type: 'string', nullable: true, example: '8:00 - 16:00'),
        new OA\Property(property: 'orden_organigrama', type: 'integer', example: 1),
        new OA\Property(property: 'activa', type: 'boolean', example: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'SecretariaPaginada',
    properties: [
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/SecretariaItem')),
        new OA\Property(property: 'total', type: 'integer', example: 12),
    ]
)]
class SecretariaSwagger
{
    #[OA\Get(
        path: '/secretarias',
        tags: ['Secretarías'],
        summary: 'Listar secretarías municipales',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'pageIndex', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'pageSize', in: 'query', schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\Parameter(name: 'query', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'activa', in: 'query', schema: new OA\Schema(type: 'boolean')),
            new OA\Parameter(name: 'sort[key]', in: 'query', schema: new OA\Schema(type: 'string', default: 'orden_organigrama')),
            new OA\Parameter(name: 'sort[order]', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'], default: 'asc')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada', content: new OA\JsonContent(ref: '#/components/schemas/SecretariaPaginada')),
        ]
    )]
    public function index() {}

    #[OA\Post(
        path: '/secretarias',
        tags: ['Secretarías'],
        summary: 'Crear una secretaría',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre'],
                properties: [
                    new OA\Property(property: 'nombre', type: 'string'),
                    new OA\Property(property: 'sigla', type: 'string', nullable: true),
                    new OA\Property(property: 'atribuciones', type: 'string', nullable: true),
                    new OA\Property(property: 'direccion_fisica', type: 'string', nullable: true),
                    new OA\Property(property: 'telefono', type: 'string', nullable: true),
                    new OA\Property(property: 'email', type: 'string', nullable: true),
                    new OA\Property(property: 'horario_atencion', type: 'string', nullable: true),
                    new OA\Property(property: 'foto_titular_url', type: 'string', nullable: true),
                    new OA\Property(property: 'orden_organigrama', type: 'integer', default: 0),
                    new OA\Property(property: 'activa', type: 'boolean', default: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Secretaría creada'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store() {}

    #[OA\Get(
        path: '/secretarias/{slug}',
        tags: ['Secretarías'],
        summary: 'Obtener secretaría por slug',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Secretaría encontrada'),
            new OA\Response(response: 404, description: 'No encontrada'),
        ]
    )]
    public function show() {}

    #[OA\Put(
        path: '/secretarias/{slug}',
        tags: ['Secretarías'],
        summary: 'Actualizar secretaría',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Secretaría actualizada'),
        ]
    )]
    public function update() {}

    #[OA\Delete(
        path: '/secretarias/{slug}',
        tags: ['Secretarías'],
        summary: 'Eliminar secretaría',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Secretaría eliminada'),
        ]
    )]
    public function destroy() {}
}
