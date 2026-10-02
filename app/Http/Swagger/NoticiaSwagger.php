<?php

namespace App\Http\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'NoticiaItem',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'titulo', type: 'string', example: 'Alcaldía inaugura nuevo parque'),
        new OA\Property(property: 'slug', type: 'string', example: 'cenefco-inaugura-nuevo-parque'),
        new OA\Property(property: 'entradilla', type: 'string', nullable: true, example: 'El nuevo parque beneficiará a más de 5.000 vecinos'),
        new OA\Property(property: 'imagen_principal_url', type: 'string', nullable: true),
        new OA\Property(property: 'estado', type: 'string', enum: ['borrador', 'publicado', 'archivado'], example: 'publicado'),
        new OA\Property(property: 'destacada', type: 'boolean', example: false),
        new OA\Property(property: 'fecha_publicacion', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'vistas', type: 'integer', example: 124),
        new OA\Property(property: 'categoria_id', type: 'integer', example: 2),
        new OA\Property(property: 'autor_id', type: 'integer', example: 1),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'NoticiaDetalle',
    allOf: [
        new OA\Schema(ref: '#/components/schemas/NoticiaItem'),
        new OA\Schema(properties: [
            new OA\Property(property: 'cuerpo', type: 'string', nullable: true),
            new OA\Property(property: 'meta_titulo', type: 'string', nullable: true),
            new OA\Property(property: 'meta_descripcion', type: 'string', nullable: true),
        ]),
    ]
)]
#[OA\Schema(
    schema: 'NoticiaPaginada',
    properties: [
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/NoticiaItem')),
        new OA\Property(property: 'total', type: 'integer', example: 42),
    ]
)]
class NoticiaSwagger
{
    #[OA\Get(
        path: '/noticias',
        tags: ['Noticias'],
        summary: 'Listar noticias paginadas',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'pageIndex', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'pageSize', in: 'query', schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\Parameter(name: 'query', in: 'query', description: 'Búsqueda FULLTEXT', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'estado', in: 'query', schema: new OA\Schema(type: 'string', enum: ['borrador', 'publicado', 'archivado'])),
            new OA\Parameter(name: 'categoria_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'destacada', in: 'query', schema: new OA\Schema(type: 'boolean')),
            new OA\Parameter(name: 'sort[key]', in: 'query', schema: new OA\Schema(type: 'string', default: 'fecha_publicacion')),
            new OA\Parameter(name: 'sort[order]', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'], default: 'desc')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada', content: new OA\JsonContent(ref: '#/components/schemas/NoticiaPaginada')),
        ]
    )]
    public function index() {}

    #[OA\Post(
        path: '/noticias',
        tags: ['Noticias'],
        summary: 'Crear una nueva noticia',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['titulo', 'categoria_id'],
                properties: [
                    new OA\Property(property: 'titulo', type: 'string', example: 'Nueva noticia municipal'),
                    new OA\Property(property: 'categoria_id', type: 'integer', example: 1),
                    new OA\Property(property: 'entradilla', type: 'string', nullable: true),
                    new OA\Property(property: 'cuerpo', type: 'string', nullable: true),
                    new OA\Property(property: 'imagen_principal_url', type: 'string', nullable: true),
                    new OA\Property(property: 'imagen_alt', type: 'string', nullable: true),
                    new OA\Property(property: 'estado', type: 'string', enum: ['borrador', 'publicado', 'archivado'], default: 'borrador'),
                    new OA\Property(property: 'destacada', type: 'boolean', default: false),
                    new OA\Property(property: 'fecha_publicacion', type: 'string', format: 'date-time', nullable: true),
                    new OA\Property(property: 'meta_titulo', type: 'string', nullable: true),
                    new OA\Property(property: 'meta_descripcion', type: 'string', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Noticia creada', content: new OA\JsonContent(ref: '#/components/schemas/NoticiaDetalle')),
            new OA\Response(response: 422, description: 'Error de validación'),
            new OA\Response(response: 403, description: 'Sin permisos'),
        ]
    )]
    public function store() {}

    #[OA\Get(
        path: '/noticias/{slug}',
        tags: ['Noticias'],
        summary: 'Obtener noticia por slug',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Noticia encontrada', content: new OA\JsonContent(ref: '#/components/schemas/NoticiaDetalle')),
            new OA\Response(response: 404, description: 'No encontrada'),
        ]
    )]
    public function show() {}

    #[OA\Put(
        path: '/noticias/{slug}',
        tags: ['Noticias'],
        summary: 'Actualizar noticia',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'titulo', type: 'string'),
                    new OA\Property(property: 'estado', type: 'string', enum: ['borrador', 'publicado', 'archivado']),
                    new OA\Property(property: 'destacada', type: 'boolean'),
                    new OA\Property(property: 'fecha_publicacion', type: 'string', format: 'date-time', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Noticia actualizada'),
            new OA\Response(response: 404, description: 'No encontrada'),
            new OA\Response(response: 403, description: 'Sin permisos'),
        ]
    )]
    public function update() {}

    #[OA\Delete(
        path: '/noticias/{slug}',
        tags: ['Noticias'],
        summary: 'Eliminar noticia (soft delete)',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Noticia eliminada'),
            new OA\Response(response: 404, description: 'No encontrada'),
            new OA\Response(response: 403, description: 'Sin permisos'),
        ]
    )]
    public function destroy() {}
}
