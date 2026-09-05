<?php
namespace App\Application\CertificadosModelo\Handlers;
use App\Application\CertificadosModelo\Commands\UpdateCertificadoModeloCommand;
use App\Domain\CertificadosModelo\Contracts\CertificadoModeloRepositoryInterface;
class UpdateCertificadoModeloHandler {
    public function __construct(private readonly CertificadoModeloRepositoryInterface $repository) {}
    public function handle(UpdateCertificadoModeloCommand $command): void {
        $this->repository->update($command->idCertmod, array_filter([
            'nombre'      => $command->nombre,
            'descripcion' => $command->descripcion,
        ], fn ($v) => $v !== null));
    }
}
