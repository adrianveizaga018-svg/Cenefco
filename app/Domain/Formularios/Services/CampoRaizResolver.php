<?php

namespace App\Domain\Formularios\Services;

final class CampoRaizResolver
{
    public const CLAVES_RAIZ = ['ci', 'nombre', 'apellido_paterno', 'apellido_materno', 'email', 'telefono'];

    private const ALIAS = [
        'ci'                 => 'ci',
        'cedula'              => 'ci',
        'cedula_identidad'    => 'ci',
        'carnet'              => 'ci',
        'carnet_identidad'    => 'ci',
        'nro_ci'              => 'ci',
        'numero_ci'           => 'ci',
        'documento_identidad' => 'ci',

        'nombre'           => 'nombre',
        'nombres'          => 'nombre',
        'nombre_completo'  => 'nombre',
        'primer_nombre'    => 'nombre',

        'apellido_paterno' => 'apellido_paterno',
        'paterno'          => 'apellido_paterno',
        'apellidopaterno'  => 'apellido_paterno',
        'apellido1'        => 'apellido_paterno',

        'apellido_materno' => 'apellido_materno',
        'materno'          => 'apellido_materno',
        'apellidomaterno'  => 'apellido_materno',
        'apellido2'        => 'apellido_materno',

        'email'             => 'email',
        'correo'            => 'email',
        'correo_electronico' => 'email',
        'mail'              => 'email',
        'e_mail'            => 'email',

        'telefono'         => 'telefono',
        'celular'          => 'telefono',
        'nro_celular'      => 'telefono',
        'numero_celular'   => 'telefono',
        'whatsapp'         => 'telefono',
        'nro_telefono'     => 'telefono',
        'numero_telefono'  => 'telefono',
    ];

    public static function resolverClave(string $nombreCampo, ?string $rolIdentidad = null): ?string
    {
        if ($rolIdentidad && in_array($rolIdentidad, self::CLAVES_RAIZ, true)) {
            return $rolIdentidad;
        }

        $normalizado = self::normalizar($nombreCampo);

        return self::ALIAS[$normalizado] ?? null;
    }

    public static function esCampoRaiz(string $nombreCampo, ?string $rolIdentidad = null): bool
    {
        return self::resolverClave($nombreCampo, $rolIdentidad) !== null;
    }

    public static function normalizar(string $valor): string
    {
        $valor = mb_strtolower(trim($valor), 'UTF-8');
        $valor = strtr($valor, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n', 'ü' => 'u',
        ]);
        $valor = preg_replace('/[^a-z0-9]+/', '_', $valor) ?? '';

        return trim($valor, '_');
    }
}
