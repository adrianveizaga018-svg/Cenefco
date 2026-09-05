<?php

namespace App\Infrastructure\Vendedores\Repositories;

use App\Application\Vendedores\DTOs\VendedorDTO;
use App\Domain\Vendedores\Contracts\VendedorRepositoryInterface;
use App\Domain\Vendedores\Exceptions\VendedorNotFoundException;
use App\Infrastructure\Vendedores\Models\Vendedor;
use App\Infrastructure\Vendedores\Services\VendedorComisionEstimadaService;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentVendedorRepository implements VendedorRepositoryInterface
{
    public function __construct(
        private readonly VendedorComisionEstimadaService $comisionEstimadaService,
    ) {}

    public function paginate(PaginationDTO $pagination, ?bool $activo = null): array
    {
        $q = Vendedor::query()
            ->select('vendedores.*')
            ->selectRaw("TRIM(CONCAT(u.nombre, ' ', COALESCE(u.apellido,''))) as usuario_nombre")
            ->selectRaw('COALESCE(com.comision_estimada, 0) as comision_estimada')
            ->leftJoin('usuarios as u', 'u.id', '=', 'vendedores.usuario_id')
            ->leftJoinSub($this->comisionEstimadaService->comisionEstimadaPorVendedorQuery(), 'com', 'com.vendedor_id', '=', 'vendedores.id');

        if ($activo !== null) {
            $q->where('vendedores.activo', $activo);
        }

        if ($pagination->query) {
            $term = '%' . $pagination->query . '%';
            $q->where(function ($sub) use ($term) {
                $sub->where('vendedores.nombre', 'like', $term)
                    ->orWhere('vendedores.apellido', 'like', $term)
                    ->orWhere('vendedores.ci', 'like', $term)
                    ->orWhere('vendedores.email', 'like', $term)
                    ->orWhere('vendedores.pagina', 'like', $term);
            });
        }

        $allowedSort = ['id', 'nombre', 'apellido', 'pagina', 'activo', 'created_at'];
        $sortKey = in_array($pagination->sortKey, $allowedSort, true)
            ? 'vendedores.' . $pagination->sortKey
            : 'vendedores.nombre';

        $paginated = $q->orderBy($sortKey, $pagination->sortOrder)
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($m) => VendedorDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): VendedorDTO
    {
        $model = Vendedor::query()
            ->select('vendedores.*')
            ->selectRaw("TRIM(CONCAT(u.nombre, ' ', COALESCE(u.apellido,''))) as usuario_nombre")
            ->leftJoin('usuarios as u', 'u.id', '=', 'vendedores.usuario_id')
            ->find($id);

        if (! $model) {
            throw new VendedorNotFoundException($id);
        }

        return VendedorDTO::fromModel($model);
    }

    public function create(array $data): VendedorDTO
    {
        $model = Vendedor::create($data);

        return VendedorDTO::fromModel($model);
    }

    public function update(int $id, array $data): VendedorDTO
    {
        $model = Vendedor::find($id);
        if (! $model) {
            throw new VendedorNotFoundException($id);
        }
        $model->update($data);

        return VendedorDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return Vendedor::destroy($ids) > 0;
    }
}
