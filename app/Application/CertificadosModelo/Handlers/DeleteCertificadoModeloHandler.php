<?php
namespace App\Application\CertificadosModelo\Handlers;
use App\Application\CertificadosModelo\Commands\DeleteCertificadoModeloCommand;
use App\Domain\CertificadosModelo\Contracts\CertificadoModeloRepositoryInterface;
class DeleteCertificadoModeloHandler {
    public function __construct(private readonly CertificadoModeloRepositoryInterface $repository) {}
    public function handle(DeleteCertificadoModeloCommand $command): void {
        $this->repository->delete($command->idCertmod);
    }
}
