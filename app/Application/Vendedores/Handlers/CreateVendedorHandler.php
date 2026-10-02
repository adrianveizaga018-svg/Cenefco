<?php

namespace App\Application\Vendedores\Handlers;

use App\Application\Vendedores\Commands\CreateVendedorCommand;
use App\Application\Vendedores\DTOs\VendedorDTO;
use App\Domain\Vendedores\Contracts\VendedorRepositoryInterface;

class CreateVendedorHandler
{
    public function __construct(private readonly VendedorRepositoryInterface $repository) {}

    public function handle(CreateVendedorCommand $command): VendedorDTO
    {
        return $this->repository->create([
            'nombre'      => $command->nombre,
            'apellido'    => $command->apellido,
            'ci'          => $command->ci,
            'telefono'    => $command->telefono,
            'email'       => $command->email,
            'foto'        => $command->foto,
            'pagina'      => $command->pagina,
            'meta_ventas' => $command->meta_ventas,
            'activo'      => $command->activo,
            'usuario_id'  => $command->usuario_id,
        ]);
    }
}
