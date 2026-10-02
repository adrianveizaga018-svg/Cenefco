<?php

namespace App\Http\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'BannerItem',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'titulo', type: 'string', example: 'Bienvenidos a la Alcaldía Municipal'),
        new OA\Property(property: 'imagen_url', type: 'string', example: 'https://...'),
        new OA\Property(property: 'url_destino', type: 'string', nullable: true),
        new OA\Property(property: 'orden', type: 'integer', example: 1),
        new OA\Property(property: 'activo', type: 'boolean', example: true),
        new OA\Property(property: 'fecha_inicio', type: 'string', format: 'date', nullable: true),
        new OA\Property(property: 'fecha_fin', type: 'string', format: 'date', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'ConfiguracionSitio',
    properties: [
        new OA\Property(property: 'site_name', type: 'string', example: 'Alcaldía Municipal'),
        new OA\Property(property: 'site_active', type: 'boolean', example: true),
        new OA\Property(property: 'contact_email', type: 'string', example: 'info@cenefco.gob.bo'),
        new OA\Property(property: 'items_per_page', type: 'integer', example: 10),
        new OA\Property(property: 'maintenance_mode', type: 'boolean', example: false),
        new OA\Property(property: 'site_logo', type: 'string', nullable: true),
    ]
)]
class PortalSwagger
{

    #[OA\Get(
        path: '/banners',
        tags: ['Banners'],
        summary: 'Listar banners del portal',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'activo', in: 'query', schema: new OA\Schema(type: 'boolean')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista de banners'),
        ]
    )]
    public function indexBanners() {}

    #[OA\Post(
        path: '/banners',
        tags: ['Banners'],
        summary: 'Crear un banner',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['titulo', 'imagen_url'],
                properties: [
                    new OA\Property(property: 'titulo', type: 'string'),
                    new OA\Property(property: 'imagen_url', type: 'string'),
                    new OA\Property(property: 'url_destino', type: 'string', nullable: true),
                    new OA\Property(property: 'orden', type: 'integer', default: 0),
                    new OA\Property(property: 'activo', type: 'boolean', default: true),
                    new OA\Property(property: 'fecha_inicio', type: 'string', format: 'date', nullable: true),
                    new OA\Property(property: 'fecha_fin', type: 'string', format: 'date', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Banner creado'),
        ]
    )]
    public function storeBanner() {}

    #[OA\Put(
        path: '/banners/{id}',
        tags: ['Banners'],
        summary: 'Actualizar banner',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Banner actualizado'),
        ]
    )]
    public function updateBanner() {}

    #[OA\Delete(
        path: '/banners/{id}',
        tags: ['Banners'],
        summary: 'Eliminar banner',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Banner eliminado'),
        ]
    )]
    public function destroyBanner() {}

    #[OA\Get(
        path: '/menus',
        tags: ['Menús'],
        summary: 'Listar menús de navegación con sus ítems',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Lista de menús'),
        ]
    )]
    public function indexMenus() {}

    #[OA\Put(
        path: '/menus/{id}',
        tags: ['Menús'],
        summary: 'Actualizar estructura de un menú',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'nombre', type: 'string'),
                    new OA\Property(property: 'items', type: 'array', items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'label', type: 'string'),
                            new OA\Property(property: 'url', type: 'string'),
                            new OA\Property(property: 'orden', type: 'integer'),
                            new OA\Property(property: 'activo', type: 'boolean'),
                        ]
                    )),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Menú actualizado'),
        ]
    )]
    public function updateMenu() {}

    #[OA\Get(
        path: '/configuracion',
        tags: ['Configuración'],
        summary: 'Obtener configuración global del portal',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Configuración actual', content: new OA\JsonContent(ref: '#/components/schemas/ConfiguracionSitio')),
        ]
    )]
    public function getConfiguracion() {}

    #[OA\Put(
        path: '/configuracion',
        tags: ['Configuración'],
        summary: 'Actualizar configuración del portal',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'site_name', type: 'string'),
                    new OA\Property(property: 'site_active', type: 'boolean'),
                    new OA\Property(property: 'contact_email', type: 'string', format: 'email'),
                    new OA\Property(property: 'items_per_page', type: 'integer'),
                    new OA\Property(property: 'maintenance_mode', type: 'boolean'),
                    new OA\Property(property: 'site_logo', type: 'string', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Configuración actualizada'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function updateConfiguracion() {}
}
