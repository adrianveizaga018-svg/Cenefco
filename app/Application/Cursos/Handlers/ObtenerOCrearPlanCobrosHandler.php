<?php

namespace App\Application\Cursos\Handlers;

use App\Application\Cursos\Commands\ObtenerOCrearPlanCobrosCommand;
use App\Application\Cursos\DTOs\PlanCobrosCursoDTO;
use App\Domain\Cursos\Contracts\CursoRepositoryInterface;
use App\Domain\PlanesAcademicos\Contracts\PlanAcademicoRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ObtenerOCrearPlanCobrosHandler
{
    public function __construct(
        private readonly CursoRepositoryInterface $cursoRepository,
        private readonly PlanAcademicoRepositoryInterface $planRepository,
    ) {}

    public function handle(ObtenerOCrearPlanCobrosCommand $command): PlanCobrosCursoDTO
    {
        return DB::transaction(function () use ($command) {
            $curso = $this->cursoRepository->findById($command->idPrograma);

            if (! $curso->id_imp) {
                throw new \RuntimeException('Este curso no tiene una apertura (id_imp) asignada. Configúrala primero.', 422);
            }

            $idMat = $this->cursoRepository->idMatDeImparticion($curso->id_imp);
            if (! $idMat) {
                throw new \RuntimeException('No se pudo determinar la materia de este curso.', 422);
            }

            $idPlan = $this->cursoRepository->planVinculadoAMateria($idMat);
            $creado = false;

            if ($idPlan === null) {
                $idPlan = $this->planRepository->siguienteIdDisponible();

                $this->planRepository->create([
                    'id_plan'    => $idPlan,
                    'id_us_reg'  => $command->idUsReg,
                    'titulo'     => $curso->nombre_programa ?? 'Plan de Cobros',
                    'costo'      => (string) ($curso->costo_monto ?? 0),
                    'nro_cuotas' => '0',
                    'estado'     => 1,
                    'fecha_reg'  => now(),
                ]);

                $this->cursoRepository->vincularPlanAMateria($idMat, $idPlan, $command->idUsReg);
                $creado = true;
            }

            $plan = $this->planRepository->findById($idPlan);

            return new PlanCobrosCursoDTO(
                id_plan:    $plan->id_plan,
                titulo:     $plan->titulo,
                costo:      (float) ($plan->costo ?? 0),
                nro_cuotas: (int) ($plan->nro_cuotas ?? 0),
                creado:     $creado,
            );
        });
    }
}
