<?php

namespace App\Http\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'DocumentoTransparenciaItem',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'titulo', type: 'string', example: 'Informe de Ejecución Presupuestaria 2025'),
        new OA\Property(property: 'slug', type: 'string', example: 'informe-ejecucion-presupuestaria-2025'),
        new OA\Property(property: 'tipo_documento_id', type: 'integer', example: 1),
        new OA\Property(property: 'secretaria_id', type: 'integer', nullable: true, example: 2),
        new OA\Property(property: 'gestion', type: 'integer', example: 2025),
        new OA\Property(property: 'fecha_publicacion', type: 'string', format: 'date', nullable: true),
        new OA\Property(property: 'archivo_url', type: 'string', nullable: true),
        new OA\Property(property: 'activo', type: 'boolean', example: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'TransparenciaPaginada',
    properties: [
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/DocumentoTransparenciaItem')),
        new OA\Property(property: 'total', type: 'integer', example: 80),
    ]
)]
class TransparenciaSwagger
{
    #[OA\Get(
        path: '/transparencia',
        tags: ['Transparencia'],
        summary: 'Listar documentos de transparencia',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'pageIndex', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'pageSize', in: 'query', schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\Parameter(name: 'query', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'tipo_documento_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'secretaria_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'gestion', in: 'query', schema: new OA\Schema(type: 'integer', example: 2025)),
            new OA\Parameter(name: 'activo', in: 'query', schema: new OA\Schema(type: 'boolean')),
            new OA\Parameter(name: 'sort[key]', in: 'query', schema: new OA\Schema(type: 'string', default: 'fecha_publicacion')),
            new OA\Parameter(name: 'sort[order]', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'], default: 'desc')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada', content: new OA\JsonContent(ref: '#/components/schemas/TransparenciaPaginada')),
        ]
    )]
    public function index() {}

    #[OA\Post(
        path: '/transparencia',
        tags: ['Transparencia'],
        summary: 'Publicar un documento de transparencia',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['titulo', 'tipo_documento_id', 'gestion'],
                properties: [
                    new OA\Property(property: 'titulo', type: 'string'),
                    new OA\Property(property: 'tipo_documento_id', type: 'integer'),
                    new OA\Property(property: 'secretaria_id', type: 'integer', nullable: true),
                    new OA\Property(property: 'descripcion', type: 'string', nullable: true),
                    new OA\Property(property: 'archivo_url', type: 'string', nullable: true),
                    new OA\Property(property: 'gestion', type: 'integer', example: 2026),
                    new OA\Property(property: 'fecha_publicacion', type: 'string', format: 'date', nullable: true),
                    new OA\Property(property: 'activo', type: 'boolean', default: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Documento creado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store() {}

    #[OA\Get(
        path: '/transparencia/{slug}',
        tags: ['Transparencia'],
        summary: 'Obtener documento por slug',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Documento encontrado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function show() {}

    #[OA\Put(
        path: '/transparencia/{slug}',
        tags: ['Transparencia'],
        summary: 'Actualizar documento',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Documento actualizado'),
        ]
    )]
    public function update() {}

    #[OA\Delete(
        path: '/transparencia/{slug}',
        tags: ['Transparencia'],
        summary: 'Eliminar documento',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Documento eliminado'),
        ]
    )]
    public function destroy() {}
}
