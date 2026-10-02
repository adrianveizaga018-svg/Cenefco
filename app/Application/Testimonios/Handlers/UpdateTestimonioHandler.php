<?php

namespace App\Application\Testimonios\Handlers;

use App\Application\Testimonios\Commands\UpdateTestimonioCommand;
use App\Application\Testimonios\DTOs\TestimonioDTO;
use App\Domain\Testimonios\Contracts\TestimonioRepositoryInterface;

class UpdateTestimonioHandler
{
    public function __construct(private readonly TestimonioRepositoryInterface $repository) {}

    public function handle(UpdateTestimonioCommand $command): TestimonioDTO
    {
        $data = array_filter([
            'nombre'       => $command->nombre,
            'testimonio'   => $command->testimonio,
            'cargo'        => $command->cargo,
            'empresa'      => $command->empresa,
            'calificacion' => $command->calificacion,
            'foto_url'     => $command->foto_url,
            'foto_alt'     => $command->foto_alt,
            'programa_id'  => $command->programa_id,
            'destacado'    => $command->destacado,
            'orden'        => $command->orden,
            'estado'       => $command->estado,
        ], fn ($v) => $v !== null);

        return $this->repository->update($command->id, $data);
    }
}
