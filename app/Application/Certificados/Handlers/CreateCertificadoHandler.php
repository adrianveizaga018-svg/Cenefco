<?php

namespace App\Application\Certificados\Handlers;

use App\Application\Certificados\Commands\CreateCertificadoCommand;
use App\Application\Certificados\DTOs\CertificadoDTO;
use App\Domain\Certificados\Contracts\CertificadoRepositoryInterface;

class CreateCertificadoHandler
{
    public function __construct(
        private readonly CertificadoRepositoryInterface $repository
    ) {}

    public function handle(CreateCertificadoCommand $command): CertificadoDTO
    {
        $row = $this->repository->create([
            'lista_aprobado_id'       => $command->lista_aprobado_id,
            'plantilla_id'            => $command->plantilla_id,
            'usuario_id'              => $command->usuario_id,
            'imparte_id'              => $command->imparte_id,
            'nombre_en_certificado'   => $command->nombre_en_certificado,
            'programa_en_certificado' => $command->programa_en_certificado,
            'condicion'               => $command->condicion,
            'nota_final'              => $command->nota_final,
            'horas_academicas'        => $command->horas_academicas,
            'fecha_inicio_curso'      => $command->fecha_inicio_curso,
            'fecha_fin_curso'         => $command->fecha_fin_curso,
            'codigo_verificacion'     => $command->codigo_verificacion,
            'qr_url'                  => $command->qr_url,
            'archivo_url'             => $command->archivo_url,
            'archivo_miniatura_url'   => $command->archivo_miniatura_url,
            'estado'                  => $command->estado,
            'created_at'              => now(),
        ]);

        return CertificadoDTO::fromRow($row);
    }
}
