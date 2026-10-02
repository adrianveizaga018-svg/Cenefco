<?php

namespace App\Application\Secretarias\Handlers;

use App\Application\Secretarias\Commands\CreateSecretariaCommand;
use App\Application\Secretarias\DTOs\SecretariaDTO;
use App\Domain\Secretarias\Contracts\SecretariaRepositoryInterface;

class CreateSecretariaHandler
{
    public function __construct(private readonly SecretariaRepositoryInterface $repository) {}

    public function handle(CreateSecretariaCommand $command): SecretariaDTO
    {
        return $this->repository->create([
            'nombre' => $command->nombre,
            'sigla' => $command->sigla,
            'atribuciones' => $command->atribuciones,
            'direccion_fisica' => $command->direccion_fisica,
            'telefono' => $command->telefono,
            'email' => $command->email,
            'horario_atencion' => $command->horario_atencion,
            'foto_titular_url' => $command->foto_titular_url,
            'orden_organigrama' => $command->orden_organigrama,
            'activa' => $command->activa,
        ]);
    }
}
