<?php

namespace App\Infrastructure\InscripcionesDiplomado\Repositories;

use App\Application\InscripcionesDiplomado\DTOs\InscripcionDiplomadoDTO;
use App\Domain\InscripcionesDiplomado\Contracts\InscripcionDiplomadoRepositoryInterface;
use App\Domain\InscripcionesDiplomado\Exceptions\InscripcionDiplomadoNotFoundException;
use App\Infrastructure\InscripcionesDiplomado\Models\InscripcionDiplomado;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Support\Facades\DB;

class EloquentInscripcionDiplomadoRepository implements InscripcionDiplomadoRepositoryInterface
{
    private function baseQuery(): \Illuminate\Database\Query\Builder
    {
        return DB::table('web_inscripcion_diplomado as d')
            ->leftJoin('web_expedido as exp', 'd.expedido_id', '=', 'exp.id')
            ->leftJoin('web_medio_pago as mp', 'd.medio_pago_id', '=', 'mp.id')
            ->leftJoin('t_ciudad as c', 'd.ciudad_residencia_id', '=', 'c.id_ciudad')
            ->leftJoin('t_programa as prog', 'd.programa_id', '=', 'prog.id_programa')
            ->select(
                'd.*',
                'exp.nombre as expedido_nombre',
                'mp.nombre as medio_pago_nombre',
                'c.nombre_ciudad as ciudad_residencia_nombre',
                'prog.nombre_programa',
            );
    }

    public function paginate(PaginationDTO $pagination, array $filters = []): array
    {
        $q = $this->baseQuery();

        if ($pagination->query) {
            $q->where(function ($sub) use ($pagination) {
                $sub->where('d.nombre', 'like', "%{$pagination->query}%")
                    ->orWhere('d.apellido_paterno', 'like', "%{$pagination->query}%")
                    ->orWhere('d.apellido_materno', 'like', "%{$pagination->query}%")
                    ->orWhere('d.email', 'like', "%{$pagination->query}%")
                    ->orWhere('d.ci', 'like', "%{$pagination->query}%")
                    ->orWhere('d.telefono_grupo_inscritos', 'like', "%{$pagination->query}%");
            });
        }

        if (! empty($filters['estado'])) {
            $q->where('d.estado', $filters['estado']);
        }

        if (! empty($filters['programa_id'])) {
            $q->where('d.programa_id', (int) $filters['programa_id']);
        }

        $total = $q->count();
        $data  = (clone $q)
            ->orderByDesc('d.created_at')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($row) => InscripcionDiplomadoDTO::fromRow($row))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): InscripcionDiplomadoDTO
    {
        $row = $this->baseQuery()->where('d.id', $id)->first();
        if (! $row) {
            throw new InscripcionDiplomadoNotFoundException($id);
        }

        return InscripcionDiplomadoDTO::fromRow($row);
    }

    public function buscarDuplicado(string $ci, ?int $programaId): ?string
    {
        if (! $programaId) {
            return null;
        }

        $pendiente = DB::table('web_inscripcion_diplomado')
            ->where('ci', $ci)
            ->where('programa_id', $programaId)
            ->whereIn('estado', ['pendiente', 'revisado', 'aceptado', 'contactado', 'inscrito'])
            ->exists();

        if ($pendiente) {
            return 'Ya enviaste una inscripción para este diplomado y está pendiente de revisión. Nuestro equipo se pondrá en contacto contigo.';
        }

        $idImp = DB::table('t_programa')->where('id_programa', $programaId)->value('id_imp');

        if ($idImp) {
            $usuarioId = DB::table('t_usuario')->where('ci', $ci)->orderByDesc('id_us')->value('id_us');

            if ($usuarioId) {
                $inscripcionActiva = DB::table('t_inscripcion')
                    ->where('id_us', $usuarioId)
                    ->where('id_imp', $idImp)
                    ->where('estado', 1)
                    ->exists();

                if ($inscripcionActiva) {
                    return 'Ya te encuentras inscrito en este diplomado.';
                }
            }
        }

        return null;
    }

    public function create(array $data): InscripcionDiplomadoDTO
    {
        $model = InscripcionDiplomado::create($data);
        return InscripcionDiplomadoDTO::fromRow(
            $this->baseQuery()->where('d.id', $model->id)->first()
        );
    }

    public function update(int $id, array $data): InscripcionDiplomadoDTO
    {
        $model = InscripcionDiplomado::findOrFail($id);
        $model->update($data);
        return InscripcionDiplomadoDTO::fromRow(
            $this->baseQuery()->where('d.id', $id)->first()
        );
    }

    public function delete(int $id): void
    {
        $deleted = InscripcionDiplomado::destroy($id);
        if (! $deleted) {
            throw new InscripcionDiplomadoNotFoundException($id);
        }
    }

    public function convertirInscripcion(int $id, ?int $idImpOverride, ?int $idPlanOverride): array
    {
        $inscripcionDiplomado = DB::table('web_inscripcion_diplomado')->where('id', $id)->first();
        if (! $inscripcionDiplomado) {
            throw new InscripcionDiplomadoNotFoundException($id);
        }

        $idImp  = $idImpOverride;
        $idPlan = $idPlanOverride;

        if ($inscripcionDiplomado->programa_id && ! $idImp) {
            $prog = DB::table('t_programa')->where('id_programa', $inscripcionDiplomado->programa_id)->first();
            if ($prog) {
                $idImp  = $idImp  ?? ($prog->id_imp  ? (int) $prog->id_imp  : null);
                $idPlan = $idPlan ?? ($prog->id_plan ? (int) $prog->id_plan : null);
            }
        }

        if (! $idImp) {
            throw new \RuntimeException(
                'El programa no tiene una impartición (apertura de curso) asignada. Asígnela en la configuración del programa.',
                422
            );
        }

        $imparte = DB::table('t_imparte')->where('id_imp', $idImp)->first();
        if (! $imparte) {
            throw new \RuntimeException('La impartición asignada al programa no existe.', 422);
        }

        return DB::transaction(function () use ($inscripcionDiplomado, $idImp, $idPlan, $imparte, $id) {
            $now       = now()->toDateTimeString();
            $usuarioId = null;

            if ($inscripcionDiplomado->ci) {
                $usuarioId = DB::table('t_usuario')
                    ->where('ci', $inscripcionDiplomado->ci)
                    ->orderByDesc('id_us')
                    ->value('id_us');
            }

            if (! $usuarioId && $inscripcionDiplomado->email) {
                $usuarioId = DB::table('t_usuario')
                    ->where('email', $inscripcionDiplomado->email)
                    ->orderByDesc('id_us')
                    ->value('id_us');
            }

            $usuarioNuevo = false;

            if (! $usuarioId) {
                $usuarioNuevo  = true;
                $nombreBase    = strtolower(
                    ($inscripcionDiplomado->nombre ?? 'usuario') . '.' .
                    ($inscripcionDiplomado->apellido_paterno ?? 'user')
                );
                $nombreUsuario = $nombreBase;
                $suffix        = 1;
                while (DB::table('t_usuario')->where('nombre_usuario', $nombreUsuario)->exists()) {
                    $nombreUsuario = $nombreBase . $suffix++;
                }

                $usuarioId = (int) (DB::table('t_usuario')->lockForUpdate()->max('id_us')) + 1;
                DB::table('t_usuario')->insert([
                    'id_us'          => $usuarioId,
                    'id_us_reg'      => 1,
                    'tipoestudiante' => '2',
                    'nombre_usuario' => $nombreUsuario,
                    'nombre'         => $inscripcionDiplomado->nombre ?? '',
                    'appaterno'      => $inscripcionDiplomado->apellido_paterno ?? null,
                    'apmaterno'      => $inscripcionDiplomado->apellido_materno ?? null,
                    'ci'             => $inscripcionDiplomado->ci ?? null,
                    'email'          => $inscripcionDiplomado->email ?? null,
                    'celular'        => $inscripcionDiplomado->telefono_grupo_inscritos ?? null,
                    'id_niv'         => 3,
                    'id_tipoprograma'=> 1,
                    'estado'         => 1,
                    'fecha_reg'      => $now,
                ]);
            }

            $inscripcionExistente = DB::table('t_inscripcion')
                ->where('id_us', $usuarioId)
                ->where('id_imp', $idImp)
                ->where('estado', 1)
                ->first();

            if ($inscripcionExistente) {
                DB::table('web_inscripcion_diplomado')
                    ->where('id', $inscripcionDiplomado->id)
                    ->update(['estado' => 'inscrito', 'updated_at' => $now]);

                $usuarioData = DB::table('t_usuario')
                    ->where('id_us', $usuarioId)
                    ->select(['id_us', 'nombre', 'appaterno', 'ci', 'email'])
                    ->first();

                return [
                    'mensaje'        => 'El estudiante ya estaba inscrito. Registro marcado como inscrito.',
                    'inscripcion_id' => $inscripcionExistente->id_ins,
                    'usuario_id'     => $usuarioId,
                    'usuario_nuevo'  => false,
                    'usuario'        => $usuarioData,
                    'imparticion'    => ['id_imp' => $idImp, 'periodo' => $imparte->periodo ?? null, 'gestion' => $imparte->gestion ?? null],
                ];
            }

            $planFinal = $idPlan;
            if (! $planFinal && $imparte->id_mat) {
                $planFinal = DB::table('t_materia_plan')
                    ->where('id_mat', $imparte->id_mat)
                    ->value('id_plan');
            }

            $idIns = DB::table('t_inscripcion')->insertGetId([
                'id_us_reg'      => 1,
                'id_us'          => $usuarioId,
                'id_imp'         => $idImp,
                'id_plan'        => $planFinal,
                'fecha_ins'      => now()->toDateString(),
                'periodo'        => $imparte->periodo ?? null,
                'gestion'        => $imparte->gestion ?? null,
                'observacion_ins'=> 'Generado desde inscripción a diplomado #' . $inscripcionDiplomado->id,
                'estado'         => 1,
                'fecha_reg'      => $now,
            ], 'id_ins');

            DB::table('web_inscripcion_diplomado')
                ->where('id', $inscripcionDiplomado->id)
                ->update(['estado' => 'inscrito', 'updated_at' => $now]);

            $usuarioData = DB::table('t_usuario')
                ->where('id_us', $usuarioId)
                ->select(['id_us', 'nombre', 'appaterno', 'ci', 'email'])
                ->first();

            return [
                'mensaje'        => 'Inscripción a diplomado convertida correctamente',
                'inscripcion_id' => $idIns,
                'usuario_id'     => $usuarioId,
                'usuario_nuevo'  => $usuarioNuevo,
                'usuario'        => $usuarioData,
                'imparticion'    => $imparte,
            ];
        });
    }
}
