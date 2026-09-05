<?php

declare(strict_types=1);

namespace App\Infrastructure\CalendarioAcademico\Repositories;

use App\Application\CalendarioAcademico\Commands\CreateCalendarioAcademicoCommand;
use App\Application\CalendarioAcademico\Commands\UpdateCalendarioAcademicoCommand;
use App\Application\CalendarioAcademico\DTOs\CalendarioAcademicoDTO;
use App\Application\CalendarioAcademico\DTOs\CursoVigenteDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;
use App\Domain\CalendarioAcademico\Contracts\CalendarioAcademicoRepositoryInterface;
use App\Domain\CalendarioAcademico\Exceptions\CalendarioAcademicoNotFoundException;
use App\Infrastructure\CalendarioAcademico\Models\CalendarioAcademico;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class EloquentCalendarioAcademicoRepository implements CalendarioAcademicoRepositoryInterface
{
    private function baseQuery(string $query = '', ?string $tipo = null, bool $soloPublicos = false)
    {
        $q = CalendarioAcademico::query()
            ->select('web_calendario_academico.*')
            ->selectRaw('prog.nombre_programa')
            ->selectRaw("NULLIF(TRIM(CONCAT(vend.nombre, ' ', vend.apellido)), '') as vendedor_nombre")
            ->leftJoin('t_programa as prog', 'prog.id_programa', '=', 'web_calendario_academico.programa_id')
            ->leftJoin('vendedores as vend', 'vend.id', '=', 'web_calendario_academico.vendedor_id')
            ->orderBy('web_calendario_academico.fecha_inicio', 'asc');

        if ($soloPublicos) {
            $q->where('web_calendario_academico.publico', true);
        }

        if ($tipo) {
            $q->where('web_calendario_academico.tipo', $tipo);
        }

        if ($query !== '') {
            $q->where(fn ($sub) => $sub
                ->where('web_calendario_academico.titulo', 'ilike', "%{$query}%")
                ->orWhere('web_calendario_academico.descripcion', 'ilike', "%{$query}%"));
        }

        return $q;
    }

    public function paginate(PaginationDTO $pagination, bool $soloPublicos = false, ?string $tipo = null): LengthAwarePaginator
    {
        return $this->baseQuery($pagination->query, $tipo, $soloPublicos)
            ->paginate(
                perPage: $pagination->pageSize,
                page: $pagination->pageIndex,
            )->through(fn ($model) => CalendarioAcademicoDTO::fromModel($model));
    }

    public function paraReporte(string $query = '', ?string $tipo = null, bool $soloPublicos = false): array
    {
        return $this->baseQuery($query, $tipo, $soloPublicos)
            ->get()
            ->map(fn ($model) => CalendarioAcademicoDTO::fromModel($model))
            ->all();
    }

    public function cursosVigentes(): array
    {
        return DB::table('t_imparte as i')
            ->leftJoin('t_materia as m', 'i.id_mat', '=', 'm.id_mat')
            ->leftJoin('t_programa as prog', function ($join) {
                $join->on('prog.id_imp', '=', 'i.id_imp')
                    ->where('prog.estado_web', 'publicado')
                    ->where('prog.estado', 1);
            })
            ->select(
                'i.id_imp', 'i.periodo', 'i.gestion', 'i.cupo',
                'i.imparte_fecha_inicio', 'i.imparte_fecha_fin',
                'm.nombre as materia_nombre',
                'prog.nombre_programa', 'prog.slug as programa_slug',
            )
            ->where('i.estado', 1)
            ->whereNotNull('i.imparte_fecha_inicio')
            ->whereNotNull('i.imparte_fecha_fin')
            ->where('i.imparte_fecha_inicio', '!=', '2000-01-01')
            ->where('i.imparte_fecha_fin', '!=', '2000-01-01')
            ->whereDate('i.imparte_fecha_inicio', '<=', now())
            ->whereDate('i.imparte_fecha_fin', '>=', now())
            ->orderBy('i.imparte_fecha_fin')
            ->get()
            ->map(fn ($row) => CursoVigenteDTO::fromRow($row))
            ->all();
    }

    public function findById(int|string $id): CalendarioAcademicoDTO
    {
        $model = CalendarioAcademico::query()
            ->select('web_calendario_academico.*')
            ->selectRaw('prog.nombre_programa')
            ->selectRaw("NULLIF(TRIM(CONCAT(vend.nombre, ' ', vend.apellido)), '') as vendedor_nombre")
            ->leftJoin('t_programa as prog', 'prog.id_programa', '=', 'web_calendario_academico.programa_id')
            ->leftJoin('vendedores as vend', 'vend.id', '=', 'web_calendario_academico.vendedor_id')
            ->where('web_calendario_academico.id', $id)
            ->first();

        if ($model === null) {
            throw new CalendarioAcademicoNotFoundException($id);
        }

        return CalendarioAcademicoDTO::fromModel($model);
    }

    public function create(CreateCalendarioAcademicoCommand $command): CalendarioAcademicoDTO
    {
        $model = CalendarioAcademico::create([
            'titulo'         => $command->titulo,
            'descripcion'    => $command->descripcion,
            'tipo'           => $command->tipo,
            'color'          => $command->color,
            'programa_id'    => $command->programa_id,
            'vendedor_id'    => $command->vendedor_id,
            'pagina'         => $command->pagina,
            'duracion_dias'  => $command->duracion_dias,
            'costo_inflado'  => $command->costo_inflado,
            'descuento'      => $command->descuento,
            'precio_vip'     => $command->precio_vip,
            'observaciones'  => $command->observaciones,
            'fecha_inicio'   => $command->fecha_inicio,
            'fecha_fin'      => $command->fecha_fin,
            'todo_el_dia'    => $command->todo_el_dia,
            'destacado'      => $command->destacado,
            'publico'        => $command->publico,
        ]);

        return CalendarioAcademicoDTO::fromModel($model);
    }

    public function update(UpdateCalendarioAcademicoCommand $command): CalendarioAcademicoDTO
    {
        $model = CalendarioAcademico::find($command->id);

        if ($model === null) {
            throw new CalendarioAcademicoNotFoundException($command->id);
        }

        $data = array_filter([
            'titulo'         => $command->titulo,
            'descripcion'    => $command->descripcion,
            'tipo'           => $command->tipo,
            'color'          => $command->color,
            'programa_id'    => $command->programa_id,
            'vendedor_id'    => $command->vendedor_id,
            'pagina'         => $command->pagina,
            'duracion_dias'  => $command->duracion_dias,
            'costo_inflado'  => $command->costo_inflado,
            'descuento'      => $command->descuento,
            'precio_vip'     => $command->precio_vip,
            'observaciones'  => $command->observaciones,
            'fecha_inicio'   => $command->fecha_inicio,
            'fecha_fin'      => $command->fecha_fin,
            'todo_el_dia'    => $command->todo_el_dia,
            'destacado'      => $command->destacado,
            'publico'        => $command->publico,
        ], fn($v) => $v !== null);

        $model->update($data);

        return CalendarioAcademicoDTO::fromModel($model->fresh());
    }

    public function delete(int|string $id): void
    {
        $model = CalendarioAcademico::find($id);

        if ($model === null) {
            throw new CalendarioAcademicoNotFoundException($id);
        }

        $model->delete();
    }
}
