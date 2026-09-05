<?php

namespace App\Application\Cursos\DTOs;

use Illuminate\Support\Str;

final readonly class EstudianteMoodleDTO
{
    public function __construct(
        public string $username,
        public string $password,
        public string $firstname,
        public string $lastname,
        public string $email,
    ) {}

    public static function fromRow(object $row): self
    {
        $email = trim((string) ($row->email ?? ''));
        $ci    = trim((string) ($row->ci ?? ''));

        [$firstname, $lastname] = self::separarNombre(
            (string) ($row->nombre ?? ''),
            (string) ($row->appaterno ?? ''),
            (string) ($row->apmaterno ?? ''),
        );

        return new self(
            username:  self::generarUsername((string) ($row->nombre_usuario ?? ''), $email, $ci),
            password:  self::generarPassword(),
            firstname: $firstname,
            lastname:  $lastname,
            email:     $email,
        );
    }

    private static function separarNombre(string $nombre, string $appaterno, string $apmaterno): array
    {
        $apellido = trim("{$appaterno} {$apmaterno}");
        if ($apellido !== '') {
            return [Str::title(trim($nombre)) ?: '-', Str::title($apellido)];
        }

        $palabras = preg_split('/\s+/', trim($nombre), -1, PREG_SPLIT_NO_EMPTY);
        if (empty($palabras)) {
            return ['-', '-'];
        }
        if (count($palabras) === 1) {
            return [Str::title($palabras[0]), '-'];
        }

        $cantidadApellido = count($palabras) >= 3 ? 2 : 1;
        $apellidoPalabras = array_splice($palabras, -$cantidadApellido);

        return [Str::title(implode(' ', $palabras)), Str::title(implode(' ', $apellidoPalabras))];
    }

    private static function generarUsername(string $nombreUsuario, string $email, string $ci): string
    {
        if ($nombreUsuario !== '') {
            return Str::slug($nombreUsuario, '.');
        }
        if ($email !== '' && str_contains($email, '@')) {
            return Str::slug(Str::before($email, '@'), '.');
        }

        return 'ci'.$ci;
    }

    private static function generarPassword(): string
    {
        $upper   = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lower   = 'abcdefghijkmnpqrstuvwxyz';
        $digits  = '23456789';
        $symbols = '!@#$%&*-_';

        $chars = [
            $upper[random_int(0, strlen($upper) - 1)],
            $lower[random_int(0, strlen($lower) - 1)],
            $digits[random_int(0, strlen($digits) - 1)],
            $symbols[random_int(0, strlen($symbols) - 1)],
        ];

        $pool = $upper.$lower.$digits.$symbols;
        while (count($chars) < 12) {
            $chars[] = $pool[random_int(0, strlen($pool) - 1)];
        }

        shuffle($chars);

        return implode('', $chars);
    }
}
