<?php

namespace App\Application\Intents\Commands;

final readonly class CreateIntentCommand
{
    public function __construct(
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
    ) {}
}
