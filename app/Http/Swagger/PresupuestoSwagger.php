<?php

namespace App\Http\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PresupuestoItem',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'secretaria_id', type: 'integer', example: 1),
        new OA\Property(property: 'gestion', type: 'integer', example: 2026),
        new OA\Property(property: 'monto_asignado', type: 'number', format: 'float', example: 1500000.00),
        new OA\Property(property: 'monto_ejecutado', type: 'number', format: 'float', nullable: true, example: 750000.00),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'PoaItem',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'secretaria_id', type: 'integer', example: 1),
        new OA\Property(property: 'gestion', type: 'integer', example: 2026),
        new OA\Property(property: 'titulo', type: 'string', example: 'Plan Operativo Anual 2026'),
        new OA\Property(property: 'archivo_url', type: 'string', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'IndicadorItem',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'nombre', type: 'string', example: 'Km de vías pavimentadas'),
        new OA\Property(property: 'categoria_id', type: 'integer', example: 1),
        new OA\Property(property: 'unidad_medida', type: 'string', example: 'km'),
        new OA\Property(property: 'meta_anual', type: 'number', format: 'float', nullable: true, example: 50.0),
        new OA\Property(property: 'activo', type: 'boolean', example: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
class PresupuestoSwagger
{

    #[OA\Get(
        path: '/presupuestos',
        tags: ['Presupuesto'],
        summary: 'Listar presupuestos por secretaría y gestión',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'secretaria_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'gestion', in: 'query', schema: new OA\Schema(type: 'integer', example: 2026)),
            new OA\Parameter(name: 'pageIndex', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'pageSize', in: 'query', schema: new OA\Schema(type: 'integer', default: 10)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista de presupuestos'),
        ]
    )]
    public function indexPresupuestos() {}

    #[OA\Post(
        path: '/presupuestos',
        tags: ['Presupuesto'],
        summary: 'Registrar presupuesto',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['secretaria_id', 'gestion', 'monto_asignado'],
                properties: [
                    new OA\Property(property: 'secretaria_id', type: 'integer'),
                    new OA\Property(property: 'gestion', type: 'integer'),
                    new OA\Property(property: 'monto_asignado', type: 'number', format: 'float'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Presupuesto registrado'),
        ]
    )]
    public function storePresupuesto() {}

    #[OA\Get(
        path: '/poa',
        tags: ['Presupuesto'],
        summary: 'Listar Planes Operativos Anuales',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'secretaria_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'gestion', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'pageIndex', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'pageSize', in: 'query', schema: new OA\Schema(type: 'integer', default: 10)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista de POA'),
        ]
    )]
    public function indexPoa() {}

    #[OA\Post(
        path: '/poa',
        tags: ['Presupuesto'],
        summary: 'Registrar un POA',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['secretaria_id', 'gestion', 'titulo'],
                properties: [
                    new OA\Property(property: 'secretaria_id', type: 'integer'),
                    new OA\Property(property: 'gestion', type: 'integer'),
                    new OA\Property(property: 'titulo', type: 'string'),
                    new OA\Property(property: 'archivo_url', type: 'string', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'POA registrado'),
        ]
    )]
    public function storePoa() {}

    #[OA\Get(
        path: '/indicadores',
        tags: ['Indicadores'],
        summary: 'Listar indicadores de gestión',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'categoria_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'activo', in: 'query', schema: new OA\Schema(type: 'boolean')),
            new OA\Parameter(name: 'pageIndex', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'pageSize', in: 'query', schema: new OA\Schema(type: 'integer', default: 10)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista de indicadores'),
        ]
    )]
    public function indexIndicadores() {}

    #[OA\Get(
        path: '/indicadores/{id}/valores',
        tags: ['Indicadores'],
        summary: 'Obtener valores históricos de un indicador',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'gestion', in: 'query', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Valores del indicador'),
            new OA\Response(response: 404, description: 'Indicador no encontrado'),
        ]
    )]
    public function valoresIndicador() {}

    #[OA\Post(
        path: '/indicadores/{id}/valores',
        tags: ['Indicadores'],
        summary: 'Registrar valor de un indicador',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['gestion', 'valor_real'],
                properties: [
                    new OA\Property(property: 'gestion', type: 'integer'),
                    new OA\Property(property: 'valor_real', type: 'number', format: 'float'),
                    new OA\Property(property: 'valor_meta', type: 'number', format: 'float', nullable: true),
                    new OA\Property(property: 'observaciones', type: 'string', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Valor registrado'),
        ]
    )]
    public function storeValor() {}
}
