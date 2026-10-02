<?php

namespace App\Domain\Trivia\Contracts;

interface TriviaPartidaRepositoryInterface
{
    public function crearPartida(int $categoriaId, int $usuarioId): mixed;

    public function findJugador(int $partidaId, int $usuarioId): mixed;

    public function actualizarProgreso(int $jugadorId, array $data): void;

    public function actualizarEstadoPartida(int $partidaId, string $estado): void;

    public function registrarRespuesta(array $data): void;

    public function preguntasRespondidasIds(int $partidaId): array;

    public function topJugadores(int $limite): array;

    public function puntajeTotalUsuario(int $usuarioId): int;

    public function crearDuelo(int $categoriaId, int $usuarioId): mixed;

    public function findPartidaPorCodigoConLock(string $codigo): mixed;

    public function findPartidaConLock(int $partidaId): mixed;

    public function agregarSegundoJugador(int $partidaId, int $usuarioId): mixed;

    public function actualizarPreguntasPartida(int $partidaId, array $preguntasIds): void;

    public function jugadoresDePartida(int $partidaId): array;

    public function otroJugador(int $partidaId, int $usuarioId): mixed;
}
