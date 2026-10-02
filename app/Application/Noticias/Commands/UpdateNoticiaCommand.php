<?php

namespace App\Application\Noticias\Commands;

final readonly class UpdateNoticiaCommand
{
    public function __construct(
        public int $id,
        public array $data,
        public array $etiquetas = [],
    ) {}
}
