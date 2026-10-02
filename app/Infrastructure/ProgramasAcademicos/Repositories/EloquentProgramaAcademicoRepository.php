<?php

namespace App\Infrastructure\ProgramasAcademicos\Repositories;

use App\Application\ProgramasAcademicos\DTOs\ProgramaAcademicoDTO;
use App\Domain\ProgramasAcademicos\Contracts\ProgramaAcademicoRepositoryInterface;
use App\Domain\ProgramasAcademicos\Exceptions\ProgramaAcademicoNotFoundException;
use App\Infrastructure\ProgramasAcademicos\Models\ProgramaAcademico;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Support\Facades\DB;

class EloquentProgramaAcademicoRepository implements ProgramaAcademicoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $conInactivos, ?int $idTipoprograma): array
    {
        $q = ProgramaAcademico::query();

        if (! $conInactivos) {
            $q->where('estado', 1);
        }

        if ($pagination->query) {
            $q->where('nombre_programa', 'like', "%{$pagination->query}%");
        }

        if ($idTipoprograma !== null) {
            $q->where('id_tipoprograma', $idTipoprograma);
        }

        $paginated = $q->orderBy('nombre_programa')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($m) => ProgramaAcademicoDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): ProgramaAcademicoDTO
    {
        $programa = ProgramaAcademico::where('id_programa', $id)->first();

        if (! $programa) {
            throw new ProgramaAcademicoNotFoundException($id);
        }

        return ProgramaAcademicoDTO::fromModel($programa);
    }

    public function create(array $data): ProgramaAcademicoDTO
    {
        $programa = ProgramaAcademico::create($data);
        return ProgramaAcademicoDTO::fromModel($programa);
    }

    public function update(int $id, array $data): ProgramaAcademicoDTO
    {
        $programa = ProgramaAcademico::where('id_programa', $id)->first();

        if (! $programa) {
            throw new ProgramaAcademicoNotFoundException($id);
        }

        $programa->update($data);
        return ProgramaAcademicoDTO::fromModel($programa->fresh());
    }

    public function delete(int $id): void
    {
        $programa = ProgramaAcademico::where('id_programa', $id)->first();

        if (! $programa) {
            throw new ProgramaAcademicoNotFoundException($id);
        }

        $programa->update(['estado' => 0]);
    }

    public function tieneInscritos(int $id): bool
    {
        $idImp = ProgramaAcademico::where('id_programa', $id)->value('id_imp');

        if (! $idImp) {
            return false;
        }

        return DB::table('t_inscripcion')->where('id_imp', $idImp)->exists();
    }
}
