<?php

namespace App\Http\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TramiteItem',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'nombre', type: 'string', example: 'Certificado de Residencia'),
        new OA\Property(property: 'slug', type: 'string', example: 'certificado-de-residencia'),
        new OA\Property(property: 'tipo_tramite_id', type: 'integer', example: 1),
        new OA\Property(property: 'unidad_responsable_id', type: 'integer', example: 2),
        new OA\Property(property: 'costo', type: 'number', format: 'float', nullable: true, example: 15.00),
        new OA\Property(property: 'moneda', type: 'string', example: 'BOB'),
        new OA\Property(property: 'dias_habiles_resolucion', type: 'integer', nullable: true, example: 3),
        new OA\Property(property: 'modalidad', type: 'string', enum: ['presencial', 'virtual', 'mixto'], example: 'presencial'),
        new OA\Property(property: 'activo', type: 'boolean', example: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'TramitePaginado',
    properties: [
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/TramiteItem')),
        new OA\Property(property: 'total', type: 'integer', example: 45),
    ]
)]
class TramiteSwagger
{
    #[OA\Get(
        path: '/tramites',
        tags: ['Trámites'],
        summary: 'Listar trámites del catálogo',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'pageIndex', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'pageSize', in: 'query', schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\Parameter(name: 'query', in: 'query', description: 'Búsqueda FULLTEXT', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'tipo_tramite_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'unidad_responsable_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'modalidad', in: 'query', schema: new OA\Schema(type: 'string', enum: ['presencial', 'virtual', 'mixto'])),
            new OA\Parameter(name: 'activo', in: 'query', schema: new OA\Schema(type: 'boolean')),
            new OA\Parameter(name: 'sort[key]', in: 'query', schema: new OA\Schema(type: 'string', default: 'nombre')),
            new OA\Parameter(name: 'sort[order]', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'], default: 'asc')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada', content: new OA\JsonContent(ref: '#/components/schemas/TramitePaginado')),
        ]
    )]
    public function index() {}

    #[OA\Post(
        path: '/tramites',
        tags: ['Trámites'],
        summary: 'Crear un trámite en el catálogo',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre', 'tipo_tramite_id', 'unidad_responsable_id'],
                properties: [
                    new OA\Property(property: 'nombre', type: 'string'),
                    new OA\Property(property: 'tipo_tramite_id', type: 'integer'),
                    new OA\Property(property: 'unidad_responsable_id', type: 'integer'),
                    new OA\Property(property: 'descripcion', type: 'string', nullable: true),
                    new OA\Property(property: 'procedimiento', type: 'string', nullable: true),
                    new OA\Property(property: 'costo', type: 'number', format: 'float', nullable: true),
                    new OA\Property(property: 'moneda', type: 'string', default: 'BOB'),
                    new OA\Property(property: 'dias_habiles_resolucion', type: 'integer', nullable: true),
                    new OA\Property(property: 'normativa_base', type: 'string', nullable: true),
                    new OA\Property(property: 'url_formulario', type: 'string', nullable: true),
                    new OA\Property(property: 'modalidad', type: 'string', enum: ['presencial', 'virtual', 'mixto'], default: 'presencial'),
                    new OA\Property(property: 'activo', type: 'boolean', default: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Trámite creado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store() {}

    #[OA\Get(
        path: '/tramites/{slug}',
        tags: ['Trámites'],
        summary: 'Obtener trámite por slug (incluye requisitos y formularios)',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Trámite encontrado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function show() {}

    #[OA\Put(
        path: '/tramites/{slug}',
        tags: ['Trámites'],
        summary: 'Actualizar trámite',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Trámite actualizado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function update() {}

    #[OA\Delete(
        path: '/tramites/{slug}',
        tags: ['Trámites'],
        summary: 'Eliminar trámite',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Trámite eliminado'),
        ]
    )]
    public function destroy() {}
}
