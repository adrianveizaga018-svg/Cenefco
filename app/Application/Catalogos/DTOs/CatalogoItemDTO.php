<?php

namespace App\Application\Catalogos\DTOs;

final readonly class CatalogoItemDTO
{
    public function __construct(
        public int $id,
        public string $label,
        public int $estado,
        public array $extra = [],
    ) {}

    public static function fromRow(object $row, string $pk, string $labelField): self
    {
        $data = (array) $row;

        return new self(
            id:     (int) $data[$pk],
            label:  (string) ($data[$labelField] ?? ''),
            estado: (int) ($data['estado'] ?? 1),
            extra:  array_diff_key($data, [$pk => '', $labelField => '', 'estado' => '']),
        );
    }
}
