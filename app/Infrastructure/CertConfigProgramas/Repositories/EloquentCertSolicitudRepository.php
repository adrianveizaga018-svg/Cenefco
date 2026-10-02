<?php

namespace App\Infrastructure\CertConfigProgramas\Repositories;

use App\Application\CertConfigProgramas\DTOs\CertSolicitudDTO;
use App\Domain\CertConfigProgramas\Contracts\CertSolicitudRepositoryInterface;
use App\Infrastructure\CertConfigProgramas\Models\CertSolicitud;
use Illuminate\Support\Facades\DB;

class EloquentCertSolicitudRepository implements CertSolicitudRepositoryInterface
{
    public function paginate(int $pageSize, int $page, ?string $estado, ?int $programaId): array
    {
        $q = DB::table('web_cert_solicitud as s')
            ->join('web_cert_config_item as i', 's.config_item_id', '=', 'i.id')
            ->join('web_cert_config_programa as c', 'i.config_id', '=', 'c.id')
            ->join('t_programa as p', function ($j) {
                $j->on('p.id_programa', '=', 'c.programa_id')
                  ->where('p.id_us_reg', '=', 0);
            })
            ->leftJoin('t_certificado as cert', 'cert.id', '=', 's.certificado_id')
            ->select('s.*', 'i.nombre_cert', 'p.nombre_programa', 'cert.archivo_url as certificado_url');

        if ($estado) {
            $q->where('s.estado', $estado);
        }

        if ($programaId) {
            $q->where('c.programa_id', $programaId);
        }

        $total  = $q->count();
        $rows   = $q->orderByDesc('s.created_at')
            ->forPage($page, $pageSize)
            ->get()
            ->map(fn ($r) => CertSolicitudDTO::fromRow($r))
            ->all();

        return ['data' => $rows, 'total' => $total];
    }

    public function findById(int $id): mixed
    {
        return DB::table('web_cert_solicitud as s')
            ->join('web_cert_config_item as i', 's.config_item_id', '=', 'i.id')
            ->join('web_cert_config_programa as c', 'i.config_id', '=', 'c.id')
            ->join('t_programa as p', function ($j) {
                $j->on('p.id_programa', '=', 'c.programa_id')
                  ->where('p.id_us_reg', '=', 0);
            })
            ->select('s.*', 'i.nombre_cert', 'p.nombre_programa')
            ->where('s.id', $id)
            ->first();
    }

    public function createMany(array $rows): array
    {
        $ids = [];
        foreach ($rows as $data) {
            $ids[] = CertSolicitud::create($data);
        }

        return array_map(fn ($m) => CertSolicitudDTO::fromRow($m), $ids);
    }

    public function update(int $id, array $data): mixed
    {
        CertSolicitud::where('id', $id)->update($data);
        return $this->findById($id);
    }

    public function countPendientes(): int
    {
        return CertSolicitud::whereIn('estado', ['pendiente_pago', 'pendiente_revision'])->count();
    }
}
