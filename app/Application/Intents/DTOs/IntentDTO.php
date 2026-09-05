<?php

namespace App\Application\Intents\DTOs;

final readonly class IntentDTO
{
    public function __construct(
        public int $id,
        public string $nombre,
        public string $slug,
        public string $dominio,
        public int $prioridad,
        public string $accion,
        public bool $activo,
        public int $orden,
        public array $eventos,
        public array $inputContexts,
        public array $outputContexts,
        public array $frasesEntrenamiento,
        public array $respuestas,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id:                  (int) $row->id,
            nombre:              $row->nombre,
            slug:                $row->slug,
            dominio:             $row->dominio,
            prioridad:           (int) $row->prioridad,
            accion:              $row->accion,
            activo:              (bool) $row->activo,
            orden:               (int) ($row->orden ?? 0),
            eventos:             is_array($row->eventos) ? $row->eventos : (json_decode($row->eventos ?? '[]', true) ?: []),
            inputContexts:       is_array($row->input_contexts) ? $row->input_contexts : (json_decode($row->input_contexts ?? '[]', true) ?: []),
            outputContexts:      is_array($row->output_contexts) ? $row->output_contexts : (json_decode($row->output_contexts ?? '[]', true) ?: []),
            frasesEntrenamiento: is_array($row->frases_entrenamiento) ? $row->frases_entrenamiento : (json_decode($row->frases_entrenamiento ?? '[]', true) ?: []),
            respuestas:          is_array($row->respuestas) ? $row->respuestas : (json_decode($row->respuestas ?? '[]', true) ?: []),
            createdAt:           isset($row->created_at) ? (string) $row->created_at : null,
            updatedAt:           isset($row->updated_at) ? (string) $row->updated_at : null,
        );
    }
}
