<?php

namespace App\Application\DocentesPerfil\DTOs;

final readonly class DocentePerfilDTO
{
    public function __construct(
        public int     $id,
        public ?int    $usuario_id,
        public string  $nombre_completo,
        public ?string $slug,
        public ?string $titulo_academico,
        public ?string $especialidad,
        public ?string $biografia,
        public ?string $foto_url,
        public ?string $foto_alt,
        public ?string $email_publico,
        public ?string $telefono,
        public ?string $linkedin_url,
        public ?string $twitter_url,
        public ?string $sitio_web_url,
        public string  $tipo,
        public bool    $mostrar_en_web,
        public int     $orden,
        public string  $estado,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}

    public static function fromModel(object $m): self
    {
        return new self(
            id:               $m->id,
            usuario_id:       $m->usuario_id ? (int) $m->usuario_id : null,
            nombre_completo:  $m->nombre_completo,
            slug:             $m->slug ?? null,
            titulo_academico: $m->titulo_academico ?? null,
            especialidad:     $m->especialidad ?? null,
            biografia:        $m->biografia ?? null,
            foto_url:         $m->foto_url ?? null,
            foto_alt:         $m->foto_alt ?? null,
            email_publico:    $m->email_publico ?? null,
            telefono:         $m->telefono ?? null,
            linkedin_url:     $m->linkedin_url ?? null,
            twitter_url:      $m->twitter_url ?? null,
            sitio_web_url:    $m->sitio_web_url ?? null,
            tipo:             $m->tipo ?? 'docente',
            mostrar_en_web:   (bool) ($m->mostrar_en_web ?? true),
            orden:            (int) ($m->orden ?? 0),
            estado:           $m->estado ?? 'publicado',
            created_at:       $m->created_at?->toIso8601String(),
            updated_at:       $m->updated_at?->toIso8601String(),
        );
    }
}
