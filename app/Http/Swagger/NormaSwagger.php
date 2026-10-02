<?php

namespace App\Http\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'NormaItem',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'numero', type: 'string', example: 'OM-001/2026'),
        new OA\Property(property: 'titulo', type: 'string', example: 'Ordenanza Municipal sobre gestión de residuos'),
        new OA\Property(property: 'slug', type: 'string', example: 'om-001-2026-gestion-residuos'),
        new OA\Property(property: 'tipo_norma_id', type: 'integer', example: 1),
        new OA\Property(property: 'fecha_promulgacion', type: 'string', format: 'date', nullable: true, example: '2026-03-15'),
        new OA\Property(property: 'estado_vigencia', type: 'string', enum: ['vigente', 'derogada', 'modificada'], example: 'vigente'),
        new OA\Property(property: 'tema_principal', type: 'string', nullable: true, example: 'Medio Ambiente'),
        new OA\Property(property: 'palabras_clave', type: 'string', nullable: true),
        new OA\Property(property: 'archivo_pdf_url', type: 'string', nullable: true),
        new OA\Property(property: 'vistas', type: 'integer', example: 58),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'NormaPaginada',
    properties: [
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/NormaItem')),
        new OA\Property(property: 'total', type: 'integer', example: 150),
    ]
)]
class NormaSwagger
{
    #[OA\Get(
        path: '/normas',
        tags: ['Normas'],
        summary: 'Listar normas paginadas',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'pageIndex', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'pageSize', in: 'query', schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\Parameter(name: 'query', in: 'query', description: 'Búsqueda FULLTEXT en título, número, resumen y texto', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'tipo_norma_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'estado_vigencia', in: 'query', schema: new OA\Schema(type: 'string', enum: ['vigente', 'derogada', 'modificada'])),
            new OA\Parameter(name: 'tema_principal', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'anio', in: 'query', description: 'Año de promulgación', schema: new OA\Schema(type: 'integer', example: 2026)),
            new OA\Parameter(name: 'sort[key]', in: 'query', schema: new OA\Schema(type: 'string', default: 'fecha_promulgacion')),
            new OA\Parameter(name: 'sort[order]', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'], default: 'desc')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada', content: new OA\JsonContent(ref: '#/components/schemas/NormaPaginada')),
        ]
    )]
    public function index() {}

    #[OA\Post(
        path: '/normas',
        tags: ['Normas'],
        summary: 'Crear una norma',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['numero', 'titulo', 'tipo_norma_id'],
                properties: [
                    new OA\Property(property: 'numero', type: 'string', example: 'OM-001/2026'),
                    new OA\Property(property: 'titulo', type: 'string'),
                    new OA\Property(property: 'tipo_norma_id', type: 'integer'),
                    new OA\Property(property: 'resumen', type: 'string', nullable: true),
                    new OA\Property(property: 'texto_completo', type: 'string', nullable: true),
                    new OA\Property(property: 'archivo_pdf_url', type: 'string', nullable: true),
                    new OA\Property(property: 'fecha_promulgacion', type: 'string', format: 'date', nullable: true),
                    new OA\Property(property: 'fecha_publicacion_gaceta', type: 'string', format: 'date', nullable: true),
                    new OA\Property(property: 'estado_vigencia', type: 'string', enum: ['vigente', 'derogada', 'modificada'], default: 'vigente'),
                    new OA\Property(property: 'tema_principal', type: 'string', nullable: true),
                    new OA\Property(property: 'palabras_clave', type: 'string', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Norma creada'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store() {}

    #[OA\Get(
        path: '/normas/{slug}',
        tags: ['Normas'],
        summary: 'Obtener norma por slug',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Norma encontrada'),
            new OA\Response(response: 404, description: 'No encontrada'),
        ]
    )]
    public function show() {}

    #[OA\Put(
        path: '/normas/{slug}',
        tags: ['Normas'],
        summary: 'Actualizar norma',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Norma actualizada'),
            new OA\Response(response: 404, description: 'No encontrada'),
        ]
    )]
    public function update() {}

    #[OA\Delete(
        path: '/normas/{slug}',
        tags: ['Normas'],
        summary: 'Eliminar norma',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Norma eliminada'),
        ]
    )]
    public function destroy() {}
}
