<?php

namespace App\Infrastructure\Catalogos\Repositories;

use App\Application\Catalogos\DTOs\CatalogoItemDTO;
use App\Domain\Catalogos\Contracts\CatalogoRepositoryInterface;
use App\Domain\Catalogos\Exceptions\CatalogoItemNotFoundException;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Support\Facades\DB;

class EloquentCatalogoRepository implements CatalogoRepositoryInterface
{
    public function paginate(string $table, string $pk, string $labelField, PaginationDTO $pagination, bool $conInactivos): array
    {
        $q = DB::table($table);

        if (! $conInactivos) {
            $q->where('estado', 1);
        }

        if ($pagination->query) {
            $q->where($labelField, 'like', "%{$pagination->query}%");
        }

        $total = $q->count();
        $items = $q->orderBy($labelField)
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get();

        return [
            'data'  => $items->map(fn ($row) => CatalogoItemDTO::fromRow($row, $pk, $labelField))->all(),
            'total' => $total,
        ];
    }

    public function findById(string $table, string $pk, int $id): CatalogoItemDTO
    {
        $row = DB::table($table)->where($pk, $id)->first();

        if (! $row) {
            throw new CatalogoItemNotFoundException($table, $id);
        }

        return CatalogoItemDTO::fromRow($row, $pk, $pk);
    }

    public function create(string $table, string $pk, string $labelField, array $data): CatalogoItemDTO
    {
        DB::table($table)->insert($data);
        $row = DB::table($table)->where($pk, $data[$pk])->first();

        return CatalogoItemDTO::fromRow($row, $pk, $labelField);
    }

    public function update(string $table, string $pk, int $id, array $data): CatalogoItemDTO
    {
        $row = DB::table($table)->where($pk, $id)->first();

        if (! $row) {
            throw new CatalogoItemNotFoundException($table, $id);
        }

        DB::table($table)->where($pk, $id)->update($data);
        $row = DB::table($table)->where($pk, $id)->first();

        return CatalogoItemDTO::fromRow($row, $pk, $pk);
    }

    public function delete(string $table, string $pk, int $id): void
    {
        $row = DB::table($table)->where($pk, $id)->first();

        if (! $row) {
            throw new CatalogoItemNotFoundException($table, $id);
        }

        DB::table($table)->where($pk, $id)->update(['estado' => 0]);
    }
}
