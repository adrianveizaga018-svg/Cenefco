<?php

namespace App\Infrastructure\Comisiones\Repositories;

use App\Application\Comisiones\DTOs\ComisionLiquidacionDTO;
use App\Domain\Comisiones\Contracts\ComisionLiquidacionRepositoryInterface;
use App\Infrastructure\Comisiones\Models\ComisionLiquidacion;
use App\Infrastructure\Comisiones\Models\ComisionLiquidacionDetalle;
use App\Infrastructure\Vendedores\Models\Vendedor;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Support\Facades\DB;

class EloquentComisionLiquidacionRepository implements ComisionLiquidacionRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, array $filters = []): array
    {
        $q = ComisionLiquidacion::query()
            ->leftJoin('vendedores as v', 'v.id', '=', 'comisiones_liquidacion.vendedor_id')
            ->select([
                'comisiones_liquidacion.*',
                DB::raw("TRIM(CONCAT(v.nombre, ' ', v.apellido)) as vendedor_nombre"),
            ]);

        if (! empty($filters['vendedor_id'])) {
            $q->where('comisiones_liquidacion.vendedor_id', (int) $filters['vendedor_id']);
        }
        if (! empty($filters['estado'])) {
            $q->where('comisiones_liquidacion.estado', $filters['estado']);
        }

        $paginated = $q->orderByDesc('comisiones_liquidacion.created_at')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($m) => ComisionLiquidacionDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): mixed
    {
        $model = ComisionLiquidacion::with('detalle.categoria')->find($id);
        if (! $model) {
            return null;
        }

        $vendedor = Vendedor::withTrashed()->find($model->vendedor_id);
        $model->vendedor_nombre = $vendedor ? trim($vendedor->nombre.' '.$vendedor->apellido) : null;

        return $model;
    }

    public function create(array $data, array $detalle): mixed
    {
        $model = ComisionLiquidacion::create($data);

        foreach ($detalle as $linea) {
            $linea['comisiones_liquidacion_id'] = $model->id;
            ComisionLiquidacionDetalle::create($linea);
        }

        return $this->findById($model->id);
    }

    public function update(int $id, array $data): mixed
    {
        $model = ComisionLiquidacion::findOrFail($id);
        $model->update($data);

        return $this->findById($model->id);
    }

    public function eliminarDetalle(int $id): void
    {
        ComisionLiquidacionDetalle::where('comisiones_liquidacion_id', $id)->delete();
    }
}
