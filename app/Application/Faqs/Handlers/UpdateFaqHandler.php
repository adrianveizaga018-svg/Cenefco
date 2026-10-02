<?php

namespace App\Application\Faqs\Handlers;

use App\Application\Faqs\Commands\UpdateFaqCommand;
use App\Application\Faqs\DTOs\FaqDTO;
use App\Domain\Faqs\Contracts\FaqRepositoryInterface;

class UpdateFaqHandler
{
    public function __construct(private readonly FaqRepositoryInterface $repository) {}

    public function handle(UpdateFaqCommand $c): FaqDTO
    {
        $data = array_filter([
            'pregunta'    => $c->pregunta,
            'respuesta'   => $c->respuesta,
            'categoria'   => $c->categoria,
            'programa_id' => $c->programa_id,
            'orden'       => $c->orden,
            'activo'      => $c->activo,
        ], fn ($v) => $v !== null);

        return $this->repository->update($c->id, $data);
    }
}
