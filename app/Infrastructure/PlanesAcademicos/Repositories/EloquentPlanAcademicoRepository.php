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
    public function paginate(PaginationDTO $pagination, bool $conInactivos, ?int $idCatplan, ?int $idMat): array
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

        $total = $q->count();
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

        $plan->update(['estado' => 0]);
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
