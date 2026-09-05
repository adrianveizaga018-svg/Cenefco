<?php

namespace App\Application\Testimonios\Handlers;

use App\Application\Testimonios\Commands\CreateTestimonioCommand;
use App\Application\Testimonios\DTOs\TestimonioDTO;
use App\Domain\Testimonios\Contracts\TestimonioRepositoryInterface;

class CreateTestimonioHandler
{
    public function __construct(private readonly TestimonioRepositoryInterface $repository) {}

    public function handle(CreateTestimonioCommand $command): TestimonioDTO
    {
        return $this->repository->create([
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
        ]);
    }
}
