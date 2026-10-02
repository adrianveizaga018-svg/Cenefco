<?php

namespace App\Application\Eventos\Handlers;

use App\Application\Eventos\Commands\UpdateEventoCommand;
use App\Application\Eventos\DTOs\EventoDTO;
use App\Domain\Eventos\Contracts\EventoRepositoryInterface;

class UpdateEventoHandler
{
    public function __construct(private readonly EventoRepositoryInterface $repository) {}

    public function handle(UpdateEventoCommand $c): EventoDTO
    {
        $data = array_filter([
            'titulo'           => $c->titulo,
            'entradilla'       => $c->entradilla,
            'descripcion'      => $c->descripcion,
            'imagen_url'       => $c->imagen_url,
            'imagen_alt'       => $c->imagen_alt,
            'tipo'             => $c->tipo,
            'modalidad'        => $c->modalidad,
            'lugar'            => $c->lugar,
            'url_transmision'  => $c->url_transmision,
            'url_registro'     => $c->url_registro,
            'gratuito'         => $c->gratuito,
            'precio'           => $c->precio,
            'cupo_maximo'      => $c->cupo_maximo,
            'programa_id'      => $c->programa_id,
            'fecha_inicio'     => $c->fecha_inicio,
            'fecha_fin'        => $c->fecha_fin,
            'todo_el_dia'      => $c->todo_el_dia,
            'destacado'        => $c->destacado,
            'meta_titulo'      => $c->meta_titulo,
            'meta_descripcion' => $c->meta_descripcion,
            'estado'           => $c->estado,
        ], fn ($v) => $v !== null);

        return $this->repository->update($c->id, $data);
    }
}
