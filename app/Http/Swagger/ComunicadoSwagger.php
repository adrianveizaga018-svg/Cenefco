<?php

namespace App\Http\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ComunicadoItem',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'titulo', type: 'string', example: 'Comunicado oficial sobre obras viales'),
        new OA\Property(property: 'slug', type: 'string', example: 'comunicado-oficial-sobre-obras-viales'),
        new OA\Property(property: 'tipo', type: 'string', enum: ['prensa', 'oficial', 'urgente'], example: 'oficial'),
        new OA\Property(property: 'imagen_url', type: 'string', nullable: true),
        new OA\Property(property: 'estado', type: 'string', enum: ['borrador', 'publicado', 'archivado'], example: 'publicado'),
        new OA\Property(property: 'fecha_publicacion', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'fecha_vigencia_hasta', type: 'string', format: 'date', nullable: true),
        new OA\Property(property: 'autor_id', type: 'integer', example: 1),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'ComunicadoPaginado',
    properties: [
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/ComunicadoItem')),
        new OA\Property(property: 'total', type: 'integer', example: 15),
    ]
)]
class ComunicadoSwagger
{
    #[OA\Get(
        path: '/comunicados',
        tags: ['Comunicados'],
        summary: 'Listar comunicados paginados',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'pageIndex', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'pageSize', in: 'query', schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\Parameter(name: 'query', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'tipo', in: 'query', schema: new OA\Schema(type: 'string', enum: ['prensa', 'oficial', 'urgente'])),
            new OA\Parameter(name: 'estado', in: 'query', schema: new OA\Schema(type: 'string', enum: ['borrador', 'publicado', 'archivado'])),
            new OA\Parameter(name: 'sort[key]', in: 'query', schema: new OA\Schema(type: 'string', default: 'fecha_publicacion')),
            new OA\Parameter(name: 'sort[order]', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'], default: 'desc')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada', content: new OA\JsonContent(ref: '#/components/schemas/ComunicadoPaginado')),
        ]
    )]
    public function index() {}

    #[OA\Post(
        path: '/comunicados',
        tags: ['Comunicados'],
        summary: 'Crear un comunicado',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['titulo'],
                properties: [
                    new OA\Property(property: 'titulo', type: 'string'),
                    new OA\Property(property: 'tipo', type: 'string', enum: ['prensa', 'oficial', 'urgente'], default: 'prensa'),
                    new OA\Property(property: 'contenido', type: 'string', nullable: true),
                    new OA\Property(property: 'imagen_url', type: 'string', nullable: true),
                    new OA\Property(property: 'estado', type: 'string', enum: ['borrador', 'publicado'], default: 'borrador'),
                    new OA\Property(property: 'fecha_publicacion', type: 'string', format: 'date-time', nullable: true),
                    new OA\Property(property: 'fecha_vigencia_hasta', type: 'string', format: 'date', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Comunicado creado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store() {}

    #[OA\Get(
        path: '/comunicados/{slug}',
        tags: ['Comunicados'],
        summary: 'Obtener comunicado por slug',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Comunicado encontrado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function show() {}

    #[OA\Put(
        path: '/comunicados/{slug}',
        tags: ['Comunicados'],
        summary: 'Actualizar comunicado',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Comunicado actualizado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function update() {}

    #[OA\Delete(
        path: '/comunicados/{slug}',
        tags: ['Comunicados'],
        summary: 'Eliminar comunicado',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Comunicado eliminado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function destroy() {}
}
