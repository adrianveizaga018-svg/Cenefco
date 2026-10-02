<?php

namespace App\Application\Normas\Handlers;

use App\Application\Normas\Commands\CreateNormaCommand;
use App\Application\Normas\DTOs\NormaDTO;
use App\Domain\Normas\Contracts\NormaRepositoryInterface;

class CreateNormaHandler
{
    public function __construct(private readonly NormaRepositoryInterface $repository) {}

    public function handle(CreateNormaCommand $command): NormaDTO
    {
        return $this->repository->create([
            'tipo_norma_id' => $command->tipo_norma_id,
            'numero' => $command->numero,
            'titulo' => $command->titulo,
            'resumen' => $command->resumen,
            'texto_completo' => $command->texto_completo,
            'archivo_pdf_url' => $command->archivo_pdf_url,
            'fecha_promulgacion' => $command->fecha_promulgacion,
            'fecha_publicacion_gaceta' => $command->fecha_publicacion_gaceta,
            'estado_vigencia' => $command->estado_vigencia,
            'tema_principal' => $command->tema_principal,
            'palabras_clave' => $command->palabras_clave,
            'publicado_por' => $command->publicado_por,
        ]);
    }
}
