<?php

namespace App\Infrastructure\Trivia\Repositories;

use App\Domain\Trivia\Contracts\TriviaCanjeRepositoryInterface;
use App\Infrastructure\Trivia\Models\TriviaCanje;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EloquentTriviaCanjeRepository implements TriviaCanjeRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, array $filters = []): array
    {
        $q = TriviaCanje::query()->with(['usuario', 'premio']);

        if (! empty($filters['estado'])) {
            $q->where('estado', $filters['estado']);
        }

        if ($pagination->query) {
            $q->where('codigo', 'like', "%{$pagination->query}%");
        }

        $paginated = $q->orderBy('created_at', 'desc')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data' => collect($paginated->items())->map(fn ($m) => \App\Application\Trivia\DTOs\TriviaCanjeDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function misCanjes(int $usuarioId): array
    {
        return TriviaCanje::query()
            ->with('premio')
            ->where('usuario_id', $usuarioId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->all();
    }

    public function findById(int $id): mixed
    {
        return TriviaCanje::with(['usuario', 'premio'])->find($id);
    }

    public function findByIdConLock(int $id): mixed
    {
        return TriviaCanje::query()->where('id', $id)->lockForUpdate()->first();
    }

    public function create(array $data): mixed
    {
        $data['codigo'] = $this->generarCodigo();
        $model = TriviaCanje::create($data);

        return $model->load('premio');
    }

    public function marcarEntregado(int $id, array $data): mixed
    {
        $model = TriviaCanje::find($id);
        $model->update($data);

        return $model->load(['usuario', 'premio']);
    }

    public function cancelar(int $id, array $data): mixed
    {
        $model = TriviaCanje::find($id);
        $model->update($data);

        return $model->load(['usuario', 'premio']);
    }

    public function puntosGastadosUsuario(int $usuarioId): int
    {
        return (int) TriviaCanje::query()
            ->where('usuario_id', $usuarioId)
            ->where('estado', '!=', 'cancelado')
            ->sum('costo_puntos');
    }

    public function existeCodigo(string $codigo): bool
    {
        return TriviaCanje::where('codigo', $codigo)->exists();
    }

    private function generarCodigo(): string
    {
        $year = now()->year;
        do {
            $random = strtoupper(Str::random(6));
            $codigo = "CNJ-{$year}-{$random}";
        } while ($this->existeCodigo($codigo));

        return $codigo;
    }
}
