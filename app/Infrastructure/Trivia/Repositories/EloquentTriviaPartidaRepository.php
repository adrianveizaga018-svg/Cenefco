<?php

namespace App\Infrastructure\Trivia\Repositories;

use App\Application\Trivia\DTOs\TriviaRankingItemDTO;
use App\Domain\Trivia\Contracts\TriviaPartidaRepositoryInterface;
use App\Infrastructure\Trivia\Models\TriviaPartida;
use App\Infrastructure\Trivia\Models\TriviaPartidaJugador;
use App\Infrastructure\Trivia\Models\TriviaPartidaRespuesta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EloquentTriviaPartidaRepository implements TriviaPartidaRepositoryInterface
{
    public function crearPartida(int $categoriaId, int $usuarioId): mixed
    {
        $partida = TriviaPartida::create([
            'modo' => 'individual',
            'categoria_id' => $categoriaId,
            'estado' => 'en_curso',
        ]);

        $jugador = TriviaPartidaJugador::create([
            'partida_id' => $partida->id,
            'usuario_id' => $usuarioId,
            'puntaje' => 0,
            'vidas' => 3,
            'estado' => 'jugando',
            'orden_turno' => 0,
        ]);

        $jugador->setRelation('partida', $partida);

        return $jugador;
    }

    public function findJugador(int $partidaId, int $usuarioId): mixed
    {
        return TriviaPartidaJugador::with('partida')
            ->where('partida_id', $partidaId)
            ->where('usuario_id', $usuarioId)
            ->first();
    }

    public function actualizarProgreso(int $jugadorId, array $data): void
    {
        TriviaPartidaJugador::where('id', $jugadorId)->update($data);
    }

    public function actualizarEstadoPartida(int $partidaId, string $estado): void
    {
        TriviaPartida::where('id', $partidaId)->update(['estado' => $estado]);
    }

    public function registrarRespuesta(array $data): void
    {
        TriviaPartidaRespuesta::create($data);
    }

    public function preguntasRespondidasIds(int $partidaId): array
    {
        return TriviaPartidaRespuesta::where('partida_id', $partidaId)->pluck('pregunta_id')->all();
    }

    public function topJugadores(int $limite): array
    {
        $filas = TriviaPartidaJugador::query()
            ->join('usuarios', 'usuarios.id', '=', 'trivia_partida_jugadores.usuario_id')
            ->select([
                'trivia_partida_jugadores.usuario_id',
                'usuarios.nombre',
                'usuarios.apellido',
                'usuarios.avatar_url',
                DB::raw('SUM(trivia_partida_jugadores.puntaje) as puntaje_total'),
                DB::raw('COUNT(*) as partidas_jugadas'),
                DB::raw("SUM(CASE WHEN trivia_partida_jugadores.estado = 'ganador' THEN 1 ELSE 0 END) as partidas_ganadas"),
            ])
            ->groupBy('trivia_partida_jugadores.usuario_id', 'usuarios.nombre', 'usuarios.apellido', 'usuarios.avatar_url')
            ->orderByDesc('puntaje_total')
            ->limit($limite)
            ->get();

        return $filas->values()
            ->map(fn ($fila, $i) => TriviaRankingItemDTO::fromRow($fila, $i + 1))
            ->all();
    }

    public function puntajeTotalUsuario(int $usuarioId): int
    {
        return (int) TriviaPartidaJugador::where('usuario_id', $usuarioId)->sum('puntaje');
    }

    public function crearDuelo(int $categoriaId, int $usuarioId): mixed
    {
        $partida = TriviaPartida::create([
            'modo' => 'duelo',
            'categoria_id' => $categoriaId,
            'estado' => 'esperando',
            'codigo_sala' => $this->generarCodigoSala(),
        ]);

        $jugador = TriviaPartidaJugador::create([
            'partida_id' => $partida->id,
            'usuario_id' => $usuarioId,
            'puntaje' => 0,
            'vidas' => 3,
            'estado' => 'esperando',
            'orden_turno' => 0,
            'pregunta_indice' => 0,
        ]);

        $jugador->setRelation('partida', $partida);

        return $jugador;
    }

    public function findPartidaPorCodigoConLock(string $codigo): mixed
    {
        return TriviaPartida::query()
            ->where('codigo_sala', $codigo)
            ->where('modo', 'duelo')
            ->lockForUpdate()
            ->first()
            ?->load('jugadores');
    }

    public function findPartidaConLock(int $partidaId): mixed
    {
        return TriviaPartida::query()->where('id', $partidaId)->lockForUpdate()->first();
    }

    public function agregarSegundoJugador(int $partidaId, int $usuarioId): mixed
    {
        return TriviaPartidaJugador::create([
            'partida_id' => $partidaId,
            'usuario_id' => $usuarioId,
            'puntaje' => 0,
            'vidas' => 3,
            'estado' => 'esperando',
            'orden_turno' => 1,
            'pregunta_indice' => 0,
        ]);
    }

    public function actualizarPreguntasPartida(int $partidaId, array $preguntasIds): void
    {
        TriviaPartida::where('id', $partidaId)->update(['preguntas_ids' => $preguntasIds]);
    }

    public function jugadoresDePartida(int $partidaId): array
    {
        return TriviaPartidaJugador::with('usuario')->where('partida_id', $partidaId)->get()->all();
    }

    public function otroJugador(int $partidaId, int $usuarioId): mixed
    {
        return TriviaPartidaJugador::with('usuario')
            ->where('partida_id', $partidaId)
            ->where('usuario_id', '!=', $usuarioId)
            ->first();
    }

    private function generarCodigoSala(): string
    {
        do {
            $codigo = strtoupper(Str::random(6));
        } while (TriviaPartida::where('codigo_sala', $codigo)->exists());

        return $codigo;
    }
}
