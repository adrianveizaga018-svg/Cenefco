<?php

namespace App\Application\DocentesPerfil\Handlers;

use App\Application\DocentesPerfil\Commands\CreateDocentePerfilCommand;
use App\Application\DocentesPerfil\DTOs\DocentePerfilDTO;
use App\Domain\DocentesPerfil\Contracts\DocentePerfilRepositoryInterface;

class CreateDocentePerfilHandler
{
    public function __construct(private readonly DocentePerfilRepositoryInterface $repository) {}

    public function handle(CreateDocentePerfilCommand $c): DocentePerfilDTO
    {
        return $this->repository->create([
            'usuario_id'       => $c->usuario_id,
            'nombre_completo'  => $c->nombre_completo,
            'titulo_academico' => $c->titulo_academico,
            'especialidad'     => $c->especialidad,
            'biografia'        => $c->biografia,
            'foto_url'         => $c->foto_url,
            'foto_alt'         => $c->foto_alt,
            'email_publico'    => $c->email_publico,
            'telefono'         => $c->telefono,
            'linkedin_url'     => $c->linkedin_url,
            'twitter_url'      => $c->twitter_url,
            'sitio_web_url'    => $c->sitio_web_url,
            'tipo'             => $c->tipo,
            'mostrar_en_web'   => $c->mostrar_en_web,
            'orden'            => $c->orden,
            'estado'           => $c->estado,
        ]);
    }
}
