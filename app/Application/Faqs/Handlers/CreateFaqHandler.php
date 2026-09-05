<?php

namespace App\Application\Faqs\Handlers;

use App\Application\Faqs\Commands\CreateFaqCommand;
use App\Application\Faqs\DTOs\FaqDTO;
use App\Domain\Faqs\Contracts\FaqRepositoryInterface;

class CreateFaqHandler
{
    public function __construct(private readonly FaqRepositoryInterface $repository) {}

    public function handle(CreateFaqCommand $c): FaqDTO
    {
        return $this->repository->create([
            'pregunta'    => $c->pregunta,
            'respuesta'   => $c->respuesta,
            'categoria'   => $c->categoria,
            'programa_id' => $c->programa_id,
            'orden'       => $c->orden,
            'activo'      => $c->activo,
        ]);
    }
}
