<?php

namespace App\Infrastructure\PlanesAcademicos\Repositories;

use App\Application\PlanesAcademicos\DTOs\PlanAcademicoDTO;
use App\Domain\PlanesAcademicos\Contracts\PlanAcademicoRepositoryInterface;
use App\Domain\PlanesAcademicos\Exceptions\PlanAcademicoNotFoundException;
use App\Infrastructure\PlanesAcademicos\Models\PlanAcademico;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Support\Facades\DB;

class EloquentPlanAcademicoRepository implements PlanAcademicoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $conInactivos, ?int $idCatplan, ?int $idMat, bool $soloValidos = false): array
    {
        $q = PlanAcademico::query();

        if (! $conInactivos) {
            $q->where('estado', 1);
        }

        if ($pagination->query) {
            $search = $pagination->query;
            $q->where(fn ($sq) => $sq
                ->where('titulo', 'like', "%{$search}%")
                ->orWhere('titulo_plan', 'like', "%{$search}%")
            );
        }

        if ($idCatplan !== null) {
            $q->where('id_catplan', $idCatplan);
        }

        if ($idMat !== null) {
            $planIds = DB::table('t_materia_plan')
                ->where('id_mat', $idMat)
                ->where('estado', 1)
                ->pluck('id_plan');
            $q->whereIn('id_plan', $planIds);
        }

        // Filtrar planes incompletos: sin costo, sin cuotas definidas o con nro_cuotas = 0
        if ($soloValidos) {
            // Un plan es inválido si:
            // - costo = 0 Y nro_cuotas > 1 (cuotas sin monto no tiene sentido)
            // Plan gratuito legítimo: costo = 0 Y nro_cuotas <= 1 → válido
            $q->where(function ($sq) {
                $sq->where('costo', '>', 0)
                   ->orWhere(function ($sub) {
                       // Gratuito contado: costo 0 pero cuota única
                       $sub->where(fn ($s) => $s->whereNull('costo')->orWhere('costo', '=', 0))
                           ->where(fn ($s) => $s->whereNull('nro_cuotas')->orWhere('nro_cuotas', '<=', 1));
                   });
            });
            $q->where(fn ($sq) => $sq
                ->whereNull('nro_cuotas')
                ->orWhere('nro_cuotas', '>', 0)
            );
            // Planes de 1 cuota (contado o gratuito) son válidos aunque no tengan filas en t_fechapago
            // Planes de más de 1 cuota deben tener al menos 1 fila en t_fechapago
            $q->where(function ($sq) {
                $sq->where('nro_cuotas', '<=', 1)
                   ->orWhere(fn ($s) => $s->whereNull('nro_cuotas'))
                   ->orWhereExists(function ($ex) {
                       $ex->from('t_fechapago')
                          ->whereColumn('t_fechapago.id_plan', 't_plan.id_plan')
                          ->where('t_fechapago.estado', 1);
                   });
            });
        }

        $total = $q->count();
        $q->withCount(['inscripciones as total_inscripciones']);
        $data  = $q->orderBy('titulo')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($p) => PlanAcademicoDTO::fromModel($p))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): PlanAcademicoDTO
    {
        $plan = PlanAcademico::where('id_plan', $id)->first();
        if (! $plan) {
            throw new PlanAcademicoNotFoundException($id);
        }

        return PlanAcademicoDTO::fromModel($plan);
    }

    public function create(array $data): PlanAcademicoDTO
    {
        $plan = PlanAcademico::create($data);

        return PlanAcademicoDTO::fromModel($plan);
    }

    public function update(int $id, array $data): PlanAcademicoDTO
    {
        $plan = PlanAcademico::where('id_plan', $id)->first();
        if (! $plan) {
            throw new PlanAcademicoNotFoundException($id);
        }

        $plan->update($data);

        return PlanAcademicoDTO::fromModel($plan);
    }

    public function delete(int $id): void
    {
        $plan = PlanAcademico::where('id_plan', $id)->first();
        if (! $plan) {
            throw new PlanAcademicoNotFoundException($id);
        }

        // Hard delete: Eliminar las cuotas primero y luego el plan
        \DB::table('t_fechapago')->where('id_plan', $id)->delete();
        $plan->delete();
    }

    public function siguienteIdDisponible(): int
    {
        $id = time();
        while (PlanAcademico::where('id_plan', $id)->exists()) {
            $id++;
        }

        return $id;
    }
}
