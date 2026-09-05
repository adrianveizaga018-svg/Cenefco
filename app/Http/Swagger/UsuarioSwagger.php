<?php

namespace App\Http\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UsuarioItem',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'nombre', type: 'string', example: 'Juan Pérez'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'juan@cenefco.gob.bo'),
        new OA\Property(property: 'tipo', type: 'string', example: 'editor'),
        new OA\Property(property: 'activo', type: 'boolean', example: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'UsuarioPaginado',
    properties: [
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/UsuarioItem')),
        new OA\Property(property: 'total', type: 'integer', example: 8),
    ]
)]
class UsuarioSwagger
{
    #[OA\Get(
        path: '/usuarios',
        tags: ['Usuarios'],
        summary: 'Listar usuarios del sistema',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'pageIndex', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'pageSize', in: 'query', schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\Parameter(name: 'query', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'activo', in: 'query', schema: new OA\Schema(type: 'boolean')),
            new OA\Parameter(name: 'sort[key]', in: 'query', schema: new OA\Schema(type: 'string', default: 'nombre')),
            new OA\Parameter(name: 'sort[order]', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'], default: 'asc')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada', content: new OA\JsonContent(ref: '#/components/schemas/UsuarioPaginado')),
            new OA\Response(response: 403, description: 'Sin permisos (usuarios.ver)'),
        ]
    )]
    public function index() {}

    #[OA\Post(
        path: '/usuarios',
        tags: ['Usuarios'],
        summary: 'Crear un usuario',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre', 'email', 'password', 'role_id'],
                properties: [
                    new OA\Property(property: 'nombre', type: 'string'),
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string'),
                    new OA\Property(property: 'role_id', type: 'integer'),
                    new OA\Property(property: 'activo', type: 'boolean', default: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Usuario creado'),
            new OA\Response(response: 422, description: 'Error de validación'),
            new OA\Response(response: 403, description: 'Sin permisos (usuarios.crear)'),
        ]
    )]
    public function store() {}

    #[OA\Put(
        path: '/usuarios/{id}',
        tags: ['Usuarios'],
        summary: 'Actualizar usuario',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'nombre', type: 'string'),
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'role_id', type: 'integer'),
                    new OA\Property(property: 'activo', type: 'boolean'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Usuario actualizado'),
            new OA\Response(response: 403, description: 'Sin permisos (usuarios.editar)'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function update() {}

    #[OA\Delete(
        path: '/usuarios/{id}',
        tags: ['Usuarios'],
        summary: 'Eliminar usuario',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Usuario eliminado'),
            new OA\Response(response: 403, description: 'Sin permisos (usuarios.eliminar)'),
        ]
    )]
    public function destroy() {}
}
