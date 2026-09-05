<?php
namespace App\Application\CertificadosModelo\Handlers;
use App\Application\CertificadosModelo\Commands\CreateCertificadoModeloCommand;
use App\Application\CertificadosModelo\DTOs\CertificadoModeloDTO;
use App\Domain\CertificadosModelo\Contracts\CertificadoModeloRepositoryInterface;
class CreateCertificadoModeloHandler {
    public function __construct(private readonly CertificadoModeloRepositoryInterface $repository) {}
    public function handle(CreateCertificadoModeloCommand $command): CertificadoModeloDTO {
        $row = $this->repository->create([
            'id_certmod'  => $command->idCertmod,
            'nombre'      => $command->nombre,
            'descripcion' => $command->descripcion,
            'id_us_reg'   => $command->idUsReg,
            'fecha_reg'   => now(),
            'estado'      => 1,
        ]);
        return CertificadoModeloDTO::fromRow($row);
    }
}
