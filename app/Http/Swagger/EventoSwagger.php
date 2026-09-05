<?php

namespace App\Http\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'EventoItem',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'titulo', type: 'string', example: 'Feria Municipal de Arte'),
        new OA\Property(property: 'slug', type: 'string', example: 'feria-municipal-de-arte'),
        new OA\Property(property: 'tipo_evento_id', type: 'integer', example: 1),
        new OA\Property(property: 'lugar', type: 'string', nullable: true, example: 'Plaza Central'),
        new OA\Property(property: 'latitud', type: 'number', format: 'float', nullable: true, example: -17.3895),
        new OA\Property(property: 'longitud', type: 'number', format: 'float', nullable: true, example: -66.1568),
        new OA\Property(property: 'fecha_inicio', type: 'string', format: 'date-time', example: '2026-04-15T09:00:00Z'),
        new OA\Property(property: 'fecha_fin', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'todo_el_dia', type: 'boolean', example: false),
        new OA\Property(property: 'estado', type: 'string', enum: ['programado', 'en_curso', 'finalizado', 'cancelado'], example: 'programado'),
        new OA\Property(property: 'publico', type: 'boolean', example: true),
        new OA\Property(property: 'url_transmision', type: 'string', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'EventoPaginado',
    properties: [
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/EventoItem')),
        new OA\Property(property: 'total', type: 'integer', example: 20),
    ]
)]
class EventoSwagger
{
    #[OA\Get(
        path: '/eventos',
        tags: ['Eventos'],
        summary: 'Listar eventos paginados',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'pageIndex', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'pageSize', in: 'query', schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\Parameter(name: 'query', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'tipo_evento_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'estado', in: 'query', schema: new OA\Schema(type: 'string', enum: ['programado', 'en_curso', 'finalizado', 'cancelado'])),
            new OA\Parameter(name: 'publico', in: 'query', schema: new OA\Schema(type: 'boolean')),
            new OA\Parameter(name: 'desde', in: 'query', description: 'Fecha inicio desde (YYYY-MM-DD)', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'hasta', in: 'query', description: 'Fecha inicio hasta (YYYY-MM-DD)', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'sort[key]', in: 'query', schema: new OA\Schema(type: 'string', default: 'fecha_inicio')),
            new OA\Parameter(name: 'sort[order]', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'], default: 'asc')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada', content: new OA\JsonContent(ref: '#/components/schemas/EventoPaginado')),
        ]
    )]
    public function index() {}

    #[OA\Post(
        path: '/eventos',
        tags: ['Eventos'],
        summary: 'Crear un evento',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['titulo', 'tipo_evento_id', 'fecha_inicio'],
                properties: [
                    new OA\Property(property: 'titulo', type: 'string'),
                    new OA\Property(property: 'tipo_evento_id', type: 'integer'),
                    new OA\Property(property: 'descripcion', type: 'string', nullable: true),
                    new OA\Property(property: 'lugar', type: 'string', nullable: true),
                    new OA\Property(property: 'latitud', type: 'number', format: 'float', nullable: true),
                    new OA\Property(property: 'longitud', type: 'number', format: 'float', nullable: true),
                    new OA\Property(property: 'fecha_inicio', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'fecha_fin', type: 'string', format: 'date-time', nullable: true),
                    new OA\Property(property: 'todo_el_dia', type: 'boolean', default: false),
                    new OA\Property(property: 'estado', type: 'string', enum: ['programado', 'en_curso', 'finalizado', 'cancelado'], default: 'programado'),
                    new OA\Property(property: 'url_transmision', type: 'string', nullable: true),
                    new OA\Property(property: 'publico', type: 'boolean', default: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Evento creado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store() {}

    #[OA\Get(
        path: '/eventos/{slug}',
        tags: ['Eventos'],
        summary: 'Obtener evento por slug',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Evento encontrado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function show() {}

    #[OA\Put(
        path: '/eventos/{slug}',
        tags: ['Eventos'],
        summary: 'Actualizar evento',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Evento actualizado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function update() {}

    #[OA\Delete(
        path: '/eventos/{slug}',
        tags: ['Eventos'],
        summary: 'Eliminar evento',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Evento eliminado'),
        ]
    )]
    public function destroy() {}
}
