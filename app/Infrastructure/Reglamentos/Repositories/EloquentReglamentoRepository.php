<?php

namespace App\Infrastructure\Reglamentos\Repositories;

use App\Domain\Reglamentos\Contracts\ReglamentoRepositoryInterface;
use Illuminate\Support\Facades\DB;

class EloquentReglamentoRepository implements ReglamentoRepositoryInterface
{
    public function findByPrograma(int $idPrograma): mixed
    {
        return DB::table('web_reglamento_programa')
            ->where('id_programa', $idPrograma)
            ->first();
    }

    public function upsert(int $idPrograma, array $data): mixed
    {
        $now    = now();
        $exists = DB::table('web_reglamento_programa')
            ->where('id_programa', $idPrograma)
            ->exists();

        if ($exists) {
            DB::table('web_reglamento_programa')
                ->where('id_programa', $idPrograma)
                ->update(array_merge($data, ['updated_at' => $now]));
        } else {
            DB::table('web_reglamento_programa')
                ->insert(array_merge(
                    ['id_programa' => $idPrograma, 'created_at' => $now, 'updated_at' => $now],
                    $data
                ));
        }

        return DB::table('web_reglamento_programa')
            ->where('id_programa', $idPrograma)
            ->first();
    }
}
