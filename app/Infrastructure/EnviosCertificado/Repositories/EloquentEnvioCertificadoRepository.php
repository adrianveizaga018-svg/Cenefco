<?php

namespace App\Infrastructure\EnviosCertificado\Repositories;

use App\Application\EnviosCertificado\DTOs\EnvioCertificadoDTO;
use App\Domain\EnviosCertificado\Contracts\EnvioCertificadoRepositoryInterface;
use App\Domain\EnviosCertificado\Exceptions\EnvioCertificadoNotFoundException;
use App\Infrastructure\EnviosCertificado\Models\EnvioCertificado;
use Illuminate\Support\Facades\DB;

class EloquentEnvioCertificadoRepository implements EnvioCertificadoRepositoryInterface
{
    public function findByInscripcion(int $idIns): array
    {
        return EnvioCertificado::where('id_ins', $idIns)
            ->orderByDesc('fecha_envio')
            ->orderByDesc('id')
            ->get()
            ->map(fn ($m) => EnvioCertificadoDTO::fromModel($m))
            ->all();
    }

    public function findById(int $id): mixed
    {
        $model = EnvioCertificado::find($id);
        if (! $model) {
            throw new EnvioCertificadoNotFoundException($id);
        }

        return $model;
    }

    public function create(array $data): mixed
    {
        return EnvioCertificado::create($data);
    }

    public function delete(int $id): bool
    {
        $model = EnvioCertificado::find($id);
        if (! $model) {
            throw new EnvioCertificadoNotFoundException($id);
        }

        return (bool) $model->delete();
    }

    public function porPeriodo(?string $fechaInicio, ?string $fechaFin, ?array $idImpPermitidos = null): \Illuminate\Support\Collection
    {
        return DB::table('envios_certificado as ec')
            ->join('t_inscripcion as ins', function ($j) {
                $j->on('ec.id_ins', '=', 'ins.id_ins')
                  ->whereRaw('ins.id_us_reg = (SELECT MIN(i2.id_us_reg) FROM t_inscripcion i2 WHERE i2.id_ins = ins.id_ins)');
            })
            ->leftJoin('t_usuario as u', function ($j) {
                $j->on('ins.id_us', '=', 'u.id_us')
                  ->whereRaw("u.id_us_reg = COALESCE(
                        (SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.id_us = u.id_us AND u2.tipoestudiante = '2'),
                        (SELECT MIN(u3.id_us_reg) FROM t_usuario u3 WHERE u3.id_us = u.id_us)
                    )");
            })
            ->leftJoin('t_imparte as imp', 'ins.id_imp', '=', 'imp.id_imp')
            ->leftJoin('t_materia as m', function ($j) {
                $j->on('imp.id_mat', '=', 'm.id_mat')
                  ->whereRaw('m.id_us_reg = (SELECT MIN(m2.id_us_reg) FROM t_materia m2 WHERE m2.id_mat = m.id_mat)');
            })
            ->when($fechaInicio, fn ($q) => $q->whereDate('ec.fecha_envio', '>=', $fechaInicio))
            ->when($fechaFin, fn ($q) => $q->whereDate('ec.fecha_envio', '<=', $fechaFin))
            ->when($idImpPermitidos !== null, fn ($q) => $q->whereIn('ins.id_imp', $idImpPermitidos))
            ->selectRaw("
                ec.id as id_documento,
                ec.fecha_envio,
                'Certificado' as tipo_documento,
                TRIM(CONCAT(COALESCE(u.nombre,''), ' ', COALESCE(u.appaterno,''))) as participante_nombre,
                u.ci as participante_ci,
                ec.ciudad_destino as participante_ciudad,
                COALESCE((SELECT p2.nombre_programa FROM t_programa p2 WHERE p2.id_imp = imp.id_imp ORDER BY p2.id_us_reg LIMIT 1), m.nombremat) as curso_nombre
            ")
            ->orderByDesc('ec.fecha_envio')
            ->get();
    }
}
