<?php

namespace App\Http\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AutoridadItem',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'nombre', type: 'string', example: 'Carlos'),
        new OA\Property(property: 'apellido', type: 'string', example: 'Rodríguez'),
        new OA\Property(property: 'slug', type: 'string', example: 'carlos-rodriguez'),
        new OA\Property(property: 'cargo', type: 'string', example: 'Secretario de Obras Públicas'),
        new OA\Property(property: 'tipo', type: 'string', enum: ['alcalde', 'concejal', 'director', 'secretario', 'otro'], example: 'secretario'),
        new OA\Property(property: 'secretaria_id', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'email_institucional', type: 'string', nullable: true),
        new OA\Property(property: 'foto_url', type: 'string', nullable: true),
        new OA\Property(property: 'orden', type: 'integer', example: 1),
        new OA\Property(property: 'activo', type: 'boolean', example: true),
        new OA\Property(property: 'fecha_inicio_cargo', type: 'string', format: 'date', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'AutoridadPaginada',
    properties: [
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/AutoridadItem')),
        new OA\Property(property: 'total', type: 'integer', example: 25),
    ]
)]
class AutoridadSwagger
{
    #[OA\Get(
        path: '/autoridades',
        tags: ['Autoridades'],
        summary: 'Listar autoridades y funcionarios',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'pageIndex', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'pageSize', in: 'query', schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\Parameter(name: 'query', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'secretaria_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'tipo', in: 'query', schema: new OA\Schema(type: 'string', enum: ['alcalde', 'concejal', 'director', 'secretario', 'otro'])),
            new OA\Parameter(name: 'activo', in: 'query', schema: new OA\Schema(type: 'boolean')),
            new OA\Parameter(name: 'sort[key]', in: 'query', schema: new OA\Schema(type: 'string', default: 'orden')),
            new OA\Parameter(name: 'sort[order]', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'], default: 'asc')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada', content: new OA\JsonContent(ref: '#/components/schemas/AutoridadPaginada')),
        ]
    )]
    public function index() {}

    #[OA\Post(
        path: '/autoridades',
        tags: ['Autoridades'],
        summary: 'Registrar una autoridad',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre', 'apellido', 'cargo', 'tipo'],
                properties: [
                    new OA\Property(property: 'nombre', type: 'string'),
                    new OA\Property(property: 'apellido', type: 'string'),
                    new OA\Property(property: 'cargo', type: 'string'),
                    new OA\Property(property: 'tipo', type: 'string', enum: ['alcalde', 'concejal', 'director', 'secretario', 'otro']),
                    new OA\Property(property: 'secretaria_id', type: 'integer', nullable: true),
                    new OA\Property(property: 'perfil_profesional', type: 'string', nullable: true),
                    new OA\Property(property: 'email_institucional', type: 'string', nullable: true),
                    new OA\Property(property: 'foto_url', type: 'string', nullable: true),
                    new OA\Property(property: 'orden', type: 'integer', default: 0),
                    new OA\Property(property: 'activo', type: 'boolean', default: true),
                    new OA\Property(property: 'fecha_inicio_cargo', type: 'string', format: 'date', nullable: true),
                    new OA\Property(property: 'fecha_fin_cargo', type: 'string', format: 'date', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Autoridad registrada'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store() {}

    #[OA\Get(
        path: '/autoridades/{slug}',
        tags: ['Autoridades'],
        summary: 'Obtener autoridad por slug',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Autoridad encontrada'),
            new OA\Response(response: 404, description: 'No encontrada'),
        ]
    )]
    public function show() {}

    #[OA\Put(
        path: '/autoridades/{slug}',
        tags: ['Autoridades'],
        summary: 'Actualizar autoridad',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Autoridad actualizada'),
        ]
    )]
    public function update() {}

    #[OA\Delete(
        path: '/autoridades/{slug}',
        tags: ['Autoridades'],
        summary: 'Eliminar autoridad',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Autoridad eliminada'),
        ]
    )]
    public function destroy() {}
}
