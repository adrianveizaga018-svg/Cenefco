<?php

namespace App\Infrastructure\MdlUsers\Repositories;

use App\Application\MdlUsers\DTOs\MdlUserDTO;
use App\Domain\MdlUsers\Contracts\MdlUserRepositoryInterface;
use App\Domain\MdlUsers\Exceptions\MdlUserNotFoundException;
use App\Infrastructure\MdlUsers\Models\MdlUser;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentMdlUserRepository implements MdlUserRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query, ?int $idUsReg, bool $conInactivos): array
    {
        $q = MdlUser::query();

        if (! $conInactivos) {
            $q->where('estado', 1);
        }
        if ($query) {
            $q->where(function ($sub) use ($query) {
                $sub->where('nombre', 'like', "%{$query}%")
                    ->orWhere('appaterno', 'like', "%{$query}%")
                    ->orWhere('ci', 'like', "%{$query}%");
            });
        }
        if ($idUsReg !== null) {
            $q->where('id_us_reg', $idUsReg);
        }

        $paginated = $q->orderBy('appaterno')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($r) => MdlUserDTO::fromRow($r))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): object
    {
        $row = MdlUser::find($id);
        if (! $row) {
            throw new MdlUserNotFoundException($id);
        }
        return $row;
    }

    public function create(array $data): object
    {
        return MdlUser::create($data);
    }

    public function update(int $id, array $data): void
    {
        $this->findById($id)->update($data);
    }

    public function delete(int $id): void
    {
        $this->findById($id)->update(['estado' => 0]);
    }
}
