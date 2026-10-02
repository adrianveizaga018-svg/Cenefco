<?php

namespace App\Application\CertConfigProgramas\QueryHandlers;

use App\Application\CertConfigProgramas\DTOs\CertConfigItemDTO;
use App\Application\CertConfigProgramas\DTOs\CertConfigProgramaDTO;
use App\Application\CertConfigProgramas\Queries\GetCertConfigByProgramaQuery;
use App\Domain\CertConfigProgramas\Contracts\CertConfigProgramaRepositoryInterface;
use Illuminate\Support\Facades\DB;

class GetCertConfigByProgramaQueryHandler
{
    public function __construct(
        private readonly CertConfigProgramaRepositoryInterface $repo,
    ) {}

    public function handle(GetCertConfigByProgramaQuery $query): ?CertConfigProgramaDTO
    {
        $config = $this->repo->findByProgramaId($query->programa_id);

        if (! $config) {
            return null;
        }

        $items = DB::table('web_cert_config_item')
            ->where('config_id', $config->id)
            ->orderBy('orden')
            ->get()
            ->map(fn ($i) => CertConfigItemDTO::fromRow($i))
            ->all();

        $nombrePrograma = DB::table('t_programa')
            ->where('id_us_reg', 0)
            ->where('id_programa', $config->programa_id)
            ->value('nombre_programa');

        return CertConfigProgramaDTO::fromRow($config, $items, $nombrePrograma);
    }
}
