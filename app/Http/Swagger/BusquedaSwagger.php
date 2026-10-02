<?php

namespace App\Http\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ResultadoBusqueda',
    properties: [
        new OA\Property(property: 'tipo', type: 'string', enum: ['noticia', 'comunicado', 'norma', 'tramite', 'evento', 'documento_transparencia'], example: 'noticia'),
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'titulo', type: 'string', example: 'Alcaldía inaugura nueva ciclovía'),
        new OA\Property(property: 'slug', type: 'string', example: 'cenefco-inaugura-nueva-ciclovia'),
        new OA\Property(property: 'estado', type: 'string', example: 'publicado'),
        new OA\Property(property: 'fecha', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'BusquedaResponse',
    properties: [
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/ResultadoBusqueda')),
        new OA\Property(property: 'total', type: 'integer', example: 8),
        new OA\Property(property: 'query', type: 'string', example: 'ciclovía'),
    ]
)]
class BusquedaSwagger
{
    #[OA\Get(
        path: '/buscar',
        tags: ['Búsqueda'],
        summary: 'Búsqueda global en todo el portal',
        description: 'Busca simultáneamente en noticias, comunicados, normas, trámites, eventos y documentos de transparencia usando la vista v_busqueda_global con FULLTEXT.',
        parameters: [
            new OA\Parameter(name: 'q', in: 'query', required: true, description: 'Texto a buscar', schema: new OA\Schema(type: 'string', example: 'presupuesto municipal')),
            new OA\Parameter(name: 'tipo', in: 'query', description: 'Filtrar por tipo de resultado', schema: new OA\Schema(type: 'string', enum: ['noticia', 'comunicado', 'norma', 'tramite', 'evento', 'documento_transparencia'])),
            new OA\Parameter(name: 'pageIndex', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'pageSize', in: 'query', schema: new OA\Schema(type: 'integer', default: 20)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Resultados de búsqueda', content: new OA\JsonContent(ref: '#/components/schemas/BusquedaResponse')),
            new OA\Response(response: 422, description: 'Parámetro q requerido'),
        ]
    )]
    public function buscar() {}

    #[OA\Get(
        path: '/ping',
        tags: ['Búsqueda'],
        summary: 'Health check de la API',
        responses: [
            new OA\Response(
                response: 200,
                description: 'API funcionando',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'ok'),
                        new OA\Property(property: 'message', type: 'string', example: 'API funcionando'),
                    ]
                )
            ),
        ]
    )]
    public function ping() {}
}
