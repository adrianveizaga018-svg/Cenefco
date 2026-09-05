<?php

namespace App\Infrastructure\Trivia\Repositories;

use App\Application\Trivia\DTOs\TriviaPreguntaDTO;
use App\Domain\Trivia\Contracts\TriviaPreguntaRepositoryInterface;
use App\Domain\Trivia\Exceptions\TriviaPreguntaNotFoundException;
use App\Infrastructure\Trivia\Models\TriviaOpcion;
use App\Infrastructure\Trivia\Models\TriviaPregunta;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentTriviaPreguntaRepository implements TriviaPreguntaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, array $filters = []): array
    {
        $q = TriviaPregunta::query()->with('opciones');

        if (! empty($filters['categoria_id'])) {
            $q->where('categoria_id', (int) $filters['categoria_id']);
        }
        if (! empty($filters['nivel_id'])) {
            $q->where('nivel_id', (int) $filters['nivel_id']);
        }
        if ($pagination->query) {
            $q->where('enunciado', 'like', "%{$pagination->query}%");
        }

        $paginated = $q->orderBy($pagination->sortKey, $pagination->sortOrder)
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data' => collect($paginated->items())->map(fn ($m) => TriviaPreguntaDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): TriviaPreguntaDTO
    {
        $model = TriviaPregunta::with('opciones')->find($id);
        if (! $model) {
            throw new TriviaPreguntaNotFoundException($id);
        }

        return TriviaPreguntaDTO::fromModel($model);
    }

    public function create(array $data, array $opciones): TriviaPreguntaDTO
    {
        $model = TriviaPregunta::create($data);

        $this->guardarOpciones($model->id, $opciones);

        return $this->findById($model->id);
    }

    public function update(int $id, array $data, ?array $opciones = null): TriviaPreguntaDTO
    {
        $model = TriviaPregunta::find($id);
        if (! $model) {
            throw new TriviaPreguntaNotFoundException($id);
        }
        $model->update($data);

        if ($opciones !== null) {
            TriviaOpcion::where('pregunta_id', $id)->delete();
            $this->guardarOpciones($id, $opciones);
        }

        return $this->findById($id);
    }

    public function delete(int|array $ids): bool
    {
        return TriviaPregunta::destroy($ids) > 0;
    }

    public function siguienteParaJuego(int $categoriaId, array $excluirIds): ?TriviaPreguntaDTO
    {
        $model = TriviaPregunta::query()
            ->with('opciones')
            ->select('trivia_preguntas.*')
            ->join('trivia_niveles', 'trivia_niveles.id', '=', 'trivia_preguntas.nivel_id')
            ->where('trivia_preguntas.categoria_id', $categoriaId)
            ->where('trivia_preguntas.activo', true)
            ->where('trivia_niveles.activo', true)
            ->whereNotIn('trivia_preguntas.id', $excluirIds)
            ->inRandomOrder()
            ->first();

        return $model ? TriviaPreguntaDTO::fromModel($model) : null;
    }

    public function seleccionarParaDuelo(int $categoriaId, int $cantidad): array
    {
        return TriviaPregunta::query()
            ->join('trivia_niveles', 'trivia_niveles.id', '=', 'trivia_preguntas.nivel_id')
            ->where('trivia_preguntas.categoria_id', $categoriaId)
            ->where('trivia_preguntas.activo', true)
            ->where('trivia_niveles.activo', true)
            ->inRandomOrder()
            ->limit($cantidad)
            ->pluck('trivia_preguntas.id')
            ->all();
    }

    private function guardarOpciones(int $preguntaId, array $opciones): void
    {
        foreach ($opciones as $orden => $opcion) {
            TriviaOpcion::create([
                'pregunta_id' => $preguntaId,
                'texto' => $opcion['texto'],
                'es_correcta' => $opcion['es_correcta'],
                'orden' => $opcion['orden'] ?? $orden,
            ]);
        }
    }
}
