<?php

namespace App\Infrastructure\Organigramas\Repositories;

use App\Application\Organigramas\DTOs\OrganigramaDTO;
use App\Domain\Organigramas\Contracts\OrganigramaRepositoryInterface;
use App\Domain\Organigramas\Exceptions\OrganigramaNotFoundException;
use App\Infrastructure\Organigramas\Models\Organigrama;

class EloquentOrganigramaRepository implements OrganigramaRepositoryInterface
{
    public function findLatest(): ?array
    {
        $raiz = Organigrama::where('vigente', true)
            ->whereNull('parent_id')
            ->orderBy('orden', 'asc')
            ->first();

        if (! $raiz) {
            return null;
        }

        return $this->buildTree($raiz->id);
    }

    public function findById(int $id): OrganigramaDTO
    {
        $model = Organigrama::find($id);
        if (! $model) {
            throw new OrganigramaNotFoundException($id);
        }

        return OrganigramaDTO::fromModel($model);
    }

    public function create(array $data): OrganigramaDTO
    {
        $model = Organigrama::create($data);

        return OrganigramaDTO::fromModel($model);
    }

    public function update(int $id, array $data): OrganigramaDTO
    {
        $model = Organigrama::find($id);
        if (! $model) {
            throw new OrganigramaNotFoundException($id);
        }
        $model->update($data);

        return OrganigramaDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return Organigrama::destroy($ids) > 0;
    }

    public function all(): array
    {
        return Organigrama::whereNull('parent_id')
            ->orderBy('orden', 'asc')
            ->get()
            ->map(fn ($m) => $this->buildTree($m->id))
            ->all();
    }

    private function buildTree(int $nodeId): array
    {
        $node = Organigrama::with(['secretaria', 'secretaria.autoridades' => function ($q) {
            $q->where('activo', true)->orderBy('orden');
        }])->find($nodeId);

        if (! $node) {
            return [];
        }

        $secretaria = $node->secretaria;
        $autoridades = $secretaria?->autoridades?->map(fn ($a) => [
            'id' => $a->id,
            'nombre' => $a->nombre.' '.$a->apellido,
            'cargo' => $a->cargo,
            'tipo' => $a->tipo,
            'foto_url' => $a->foto_url,
        ])->all() ?? [];

        $children = Organigrama::where('parent_id', $nodeId)
            ->orderBy('orden', 'asc')
            ->pluck('id')
            ->map(fn ($id) => $this->buildTree($id))
            ->all();

        return [
            'id' => $node->id,
            'nivel' => $node->nivel,
            'orden' => $node->orden,
            'vigente' => (bool) $node->vigente,
            'fecha_actualizacion' => $node->fecha_actualizacion?->toDateString(),
            'secretaria' => $secretaria ? [
                'id' => $secretaria->id,
                'nombre' => $secretaria->nombre,
                'sigla' => $secretaria->sigla,
                'email' => $secretaria->email,
            ] : null,
            'autoridades' => $autoridades,
            'children' => $children,
        ];
    }
}
