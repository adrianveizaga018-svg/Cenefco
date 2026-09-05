<?php

namespace App\Infrastructure\CorreosEnviados\Repositories;

use App\Application\CorreosEnviados\DTOs\CorreoEnviadoDTO;
use App\Domain\CorreosEnviados\Contracts\CorreoEnviadoRepositoryInterface;
use App\Infrastructure\CorreosEnviados\Models\CorreoEnviado;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentCorreoEnviadoRepository implements CorreoEnviadoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $referenciaTipo = null, ?int $referenciaId = null): array
    {
        $q = CorreoEnviado::query();

        if ($referenciaTipo) {
            $q->where('referencia_tipo', $referenciaTipo);
        }

        if ($referenciaId) {
            $q->where('referencia_id', $referenciaId);
        }

        if ($pagination->query) {
            $q->where(function ($sub) use ($pagination) {
                $sub->where('destinatario', 'like', "%{$pagination->query}%")
                    ->orWhere('asunto', 'like', "%{$pagination->query}%")
                    ->orWhere('tipo', 'like', "%{$pagination->query}%");
            });
        }

        $paginated = $q->orderByDesc('created_at')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($c) => CorreoEnviadoDTO::fromModel($c))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function create(array $data): mixed
    {
        return CorreoEnviado::create($data);
    }
}
