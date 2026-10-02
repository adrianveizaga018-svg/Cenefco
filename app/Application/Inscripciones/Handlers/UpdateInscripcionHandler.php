<?php

namespace App\Application\Inscripciones\Handlers;

use App\Application\Inscripciones\Commands\UpdateInscripcionCommand;
use App\Application\Inscripciones\DTOs\InscripcionDTO;
use App\Domain\Inscripciones\Contracts\InscripcionRepositoryInterface;
use App\Services\DocumentoEstudianteArchivador;

class UpdateInscripcionHandler
{
    public function __construct(
        private readonly InscripcionRepositoryInterface $repository,
        private readonly DocumentoEstudianteArchivador $archivador,
    ) {}

    public function handle(UpdateInscripcionCommand $command): InscripcionDTO
    {
        $documentos = $command->documentos;

        if ($documentos) {
            $actual = $this->repository->findById($command->id);
            $ci     = $this->repository->ciDeUsuario($actual->id_us);

            if ($ci) {
                foreach ($documentos as $campo => $url) {
                    $documentos[$campo] = $this->archivador->archivar($url, $ci, 'inscripcion', $actual->id_imp, (string) $campo);
                }
            }
        }

        $data = array_filter([
            'fecha_ins'       => $command->fecha_ins,
            'observacion_ins' => $command->observacion_ins,
            'observacion'     => $command->observacion,
            'estado'          => $command->estado,
            'documentos'      => $documentos,
            'campos_extra'    => $command->campos_extra,
        ], fn ($v) => $v !== null);

        if ($command->clearIdPlan) {
            $data['id_plan'] = null;
        } elseif ($command->id_plan !== null) {
            $data['id_plan'] = $command->id_plan;
        }

        return $this->repository->update($command->id, $data);
    }
}
