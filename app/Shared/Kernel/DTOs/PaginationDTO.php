<?php

namespace App\Shared\Kernel\DTOs;

final readonly class PaginationDTO
{
    private const ALLOWED_SORT_COLUMNS = [
        'id', 'created_at', 'updated_at', 'orden', 'nombre', 'titulo', 'slug',
        'fecha_inicio', 'fecha_fin', 'fecha_publicacion', 'activo', 'destacado',
        'codigo', 'email', 'estado', 'tipo', 'plataforma', 'vistas', 'descargas',
        'posicion', 'delay_segundos', 'clave', 'modulo', 'descripcion','monto', 'fecha',
        'nombre_completo', 'sueldo_mensual', 'cargo', 'anio', 'mes', 'total',
        'apellido', 'pagina',
    ];

    public function __construct(
        public int $pageIndex = 1,
        public int $pageSize = 10,
        public string $query = '',
        public string $sortKey = '',
        public string $sortOrder = 'asc',
    ) {}

    public static function fromArray(array $data, string $defaultSortKey = 'created_at'): self
    {
        $sort = $data['sort'] ?? [];

        $sortKey = (string) ($sort['key'] ?? $data['sortKey'] ?? $defaultSortKey);
        if ($sortKey === '' || ! in_array($sortKey, self::ALLOWED_SORT_COLUMNS, true)) {
            $sortKey = $defaultSortKey;
        }

        $sortOrder = (string) ($sort['order'] ?? $data['sortOrder'] ?? 'asc');
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc'], true)
            ? strtolower($sortOrder)
            : 'asc';

        return new self(
            pageIndex: max(1, (int) ($data['pageIndex'] ?? 1)),
            pageSize: min(100, max(1, (int) ($data['pageSize'] ?? 10))),
            query: (string) ($data['query'] ?? ''),
            sortKey: $sortKey,
            sortOrder: $sortOrder,
        );
    }
}
