<?php

namespace App\Infrastructure\Honorarios\Repositories;

use App\Application\Honorarios\DTOs\ConfigHonorarioDTO;
use App\Domain\Honorarios\Contracts\ConfigHonorarioRepositoryInterface;
use App\Domain\Honorarios\Exceptions\ConfigHonorarioNotFoundException;
use App\Infrastructure\Honorarios\Models\ConfigHonorarioPrograma;
use Illuminate\Support\Facades\DB;

class EloquentConfigHonorarioRepository implements ConfigHonorarioRepositoryInterface
{
    public function findAll(): array
    {
        return DB::table('web_config_honorario_programa as c')
            ->leftJoin('t_programa as p', 'p.id_programa', '=', 'c.id_programa')
            ->select(['c.*', 'p.nombre_programa'])
            ->orderBy('p.nombre_programa')
            ->get()
            ->map(fn ($row) => ConfigHonorarioDTO::fromModel($row))
            ->all();
    }

    public function findByPrograma(int $idPrograma): ConfigHonorarioDTO
    {
        $row = DB::table('web_config_honorario_programa as c')
            ->leftJoin('t_programa as p', 'p.id_programa', '=', 'c.id_programa')
            ->select(['c.*', 'p.nombre_programa'])
            ->where('c.id_programa', $idPrograma)
            ->first();

        if (! $row) {
            throw new ConfigHonorarioNotFoundException($idPrograma);
        }

        return ConfigHonorarioDTO::fromModel($row);
    }

    public function upsert(int $idPrograma, array $data): ConfigHonorarioDTO
    {
        $m = ConfigHonorarioPrograma::updateOrCreate(
            ['id_programa' => $idPrograma],
            $data
        );

        return $this->findByPrograma($m->id_programa);
    }

    public function delete(int $idPrograma): bool
    {
        $m = ConfigHonorarioPrograma::where('id_programa', $idPrograma)->first();
        if (! $m) {
            throw new ConfigHonorarioNotFoundException($idPrograma);
        }

        return (bool) $m->delete();
    }
}
