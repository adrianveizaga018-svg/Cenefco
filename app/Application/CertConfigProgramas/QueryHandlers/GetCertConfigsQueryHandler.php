<?php

namespace App\Application\CertConfigProgramas\QueryHandlers;

use App\Application\CertConfigProgramas\DTOs\CertConfigItemDTO;
use App\Application\CertConfigProgramas\DTOs\CertConfigProgramaDTO;
use App\Application\CertConfigProgramas\Queries\GetCertConfigsQuery;
use App\Domain\CertConfigProgramas\Contracts\CertConfigProgramaRepositoryInterface;
use Illuminate\Support\Facades\DB;

class GetCertConfigsQueryHandler
{
    public function __construct(
        private readonly CertConfigProgramaRepositoryInterface $repo,
    ) {}

    public function handle(GetCertConfigsQuery $query): array
    {
        $configs = $this->repo->all();

        $programaIds = array_map(fn ($row) => $row->programa_id, $configs);
        $nombresPorPrograma = DB::table('t_programa')
            ->where('id_us_reg', 0)
            ->whereIn('id_programa', $programaIds)
            ->pluck('nombre_programa', 'id_programa');

        return array_map(function ($row) use ($nombresPorPrograma) {
            $items = DB::table('web_cert_config_item')
                ->where('config_id', $row->id)
                ->orderBy('orden')
                ->get()
                ->map(fn ($i) => CertConfigItemDTO::fromRow($i))
                ->all();

            return CertConfigProgramaDTO::fromRow($row, $items, $nombresPorPrograma[$row->programa_id] ?? null);
        }, $configs);
    }
}
