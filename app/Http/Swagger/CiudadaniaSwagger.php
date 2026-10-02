<?php

namespace App\Http\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ConsultaItem',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'titulo', type: 'string', example: '¿Apoya el nuevo plan de movilidad urbana?'),
        new OA\Property(property: 'descripcion', type: 'string', nullable: true),
        new OA\Property(property: 'estado', type: 'string', enum: ['activa', 'cerrada', 'borrador'], example: 'activa'),
        new OA\Property(property: 'fecha_inicio', type: 'string', format: 'date', nullable: true),
        new OA\Property(property: 'fecha_fin', type: 'string', format: 'date', nullable: true),
        new OA\Property(property: 'total_votos', type: 'integer', example: 312),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]

#[OA\Schema(
    schema: 'SugerenciaItem',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'nombre_ciudadano', type: 'string', nullable: true, example: 'Ana García'),
        new OA\Property(property: 'email', type: 'string', nullable: true, example: 'ana@correo.com'),
        new OA\Property(property: 'asunto', type: 'string', example: 'Mejora de parques'),
        new OA\Property(property: 'mensaje', type: 'string'),
        new OA\Property(property: 'estado', type: 'string', enum: ['pendiente', 'revisada', 'atendida'], example: 'pendiente'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]

#[OA\Schema(
    schema: 'SolicitudItem',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'nombre_solicitante', type: 'string', example: 'Pedro Flores'),
        new OA\Property(property: 'ci', type: 'string', nullable: true, example: '12345678'),
        new OA\Property(property: 'email', type: 'string', example: 'pedro@correo.com'),
        new OA\Property(property: 'telefono', type: 'string', nullable: true),
        new OA\Property(property: 'descripcion_solicitud', type: 'string'),
        new OA\Property(property: 'estado', type: 'string', enum: ['recibida', 'en_proceso', 'respondida', 'denegada'], example: 'recibida'),
        new OA\Property(property: 'fecha_limite_respuesta', type: 'string', format: 'date', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
class CiudadaniaSwagger
{

    #[OA\Get(
        path: '/consultas',
        tags: ['Consultas'],
        summary: 'Listar consultas ciudadanas',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'pageIndex', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'pageSize', in: 'query', schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\Parameter(name: 'estado', in: 'query', schema: new OA\Schema(type: 'string', enum: ['activa', 'cerrada', 'borrador'])),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista de consultas'),
        ]
    )]
    public function indexConsultas() {}

    #[OA\Post(
        path: '/consultas',
        tags: ['Consultas'],
        summary: 'Crear una consulta ciudadana',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['titulo'],
                properties: [
                    new OA\Property(property: 'titulo', type: 'string'),
                    new OA\Property(property: 'descripcion', type: 'string', nullable: true),
                    new OA\Property(property: 'estado', type: 'string', enum: ['activa', 'cerrada', 'borrador'], default: 'borrador'),
                    new OA\Property(property: 'fecha_inicio', type: 'string', format: 'date', nullable: true),
                    new OA\Property(property: 'fecha_fin', type: 'string', format: 'date', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Consulta creada'),
        ]
    )]
    public function storeConsulta() {}

    #[OA\Post(
        path: '/consultas/{id}/votar',
        tags: ['Consultas'],
        summary: 'Emitir voto en una consulta ciudadana',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['opcion_id'],
                properties: [
                    new OA\Property(property: 'opcion_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Voto registrado'),
            new OA\Response(response: 409, description: 'Ya votó en esta consulta'),
            new OA\Response(response: 404, description: 'Consulta no encontrada o cerrada'),
        ]
    )]
    public function votar() {}

    #[OA\Get(
        path: '/sugerencias',
        tags: ['Sugerencias'],
        summary: 'Listar sugerencias ciudadanas',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'pageIndex', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'pageSize', in: 'query', schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\Parameter(name: 'estado', in: 'query', schema: new OA\Schema(type: 'string', enum: ['pendiente', 'revisada', 'atendida'])),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista de sugerencias'),
        ]
    )]
    public function indexSugerencias() {}

    #[OA\Post(
        path: '/sugerencias',
        tags: ['Sugerencias'],
        summary: 'Enviar una sugerencia ciudadana (endpoint público)',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['asunto', 'mensaje'],
                properties: [
                    new OA\Property(property: 'nombre_ciudadano', type: 'string', nullable: true),
                    new OA\Property(property: 'email', type: 'string', format: 'email', nullable: true),
                    new OA\Property(property: 'telefono', type: 'string', nullable: true),
                    new OA\Property(property: 'asunto', type: 'string'),
                    new OA\Property(property: 'mensaje', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Sugerencia recibida'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function storeSugerencia() {}

    #[OA\Get(
        path: '/solicitudes-informacion',
        tags: ['Solicitudes'],
        summary: 'Listar solicitudes de información pública',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'pageIndex', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'pageSize', in: 'query', schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\Parameter(name: 'estado', in: 'query', schema: new OA\Schema(type: 'string', enum: ['recibida', 'en_proceso', 'respondida', 'denegada'])),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista de solicitudes'),
        ]
    )]
    public function indexSolicitudes() {}

    #[OA\Post(
        path: '/solicitudes-informacion',
        tags: ['Solicitudes'],
        summary: 'Presentar una solicitud de información pública (endpoint público)',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre_solicitante', 'email', 'descripcion_solicitud'],
                properties: [
                    new OA\Property(property: 'nombre_solicitante', type: 'string'),
                    new OA\Property(property: 'ci', type: 'string', nullable: true),
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'telefono', type: 'string', nullable: true),
                    new OA\Property(property: 'descripcion_solicitud', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Solicitud registrada'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function storeSolicitud() {}

    #[OA\Post(
        path: '/contacto',
        tags: ['Contacto'],
        summary: 'Enviar mensaje de contacto (endpoint público)',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre', 'email', 'asunto', 'mensaje'],
                properties: [
                    new OA\Property(property: 'nombre', type: 'string', example: 'María López'),
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'telefono', type: 'string', nullable: true),
                    new OA\Property(property: 'asunto', type: 'string'),
                    new OA\Property(property: 'mensaje', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Mensaje enviado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function storeContacto() {}
}
