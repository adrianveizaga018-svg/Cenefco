<?php

namespace App\Shared\Kernel\Support;

/**
 * Formato único para los datos personales que se guardan:
 * nombres en "Nombre Propio", correo en minúsculas, carnet en mayúsculas.
 * Los certificados los imprimen en MAYÚSCULAS por su cuenta.
 */
final class FormatoPersona
{
    /** "  KENY   kartman " → "Keny Kartman" */
    public static function nombre(?string $valor): ?string
    {
        $limpio = preg_replace('/\s+/u', ' ', trim((string) $valor));

        return $limpio === '' ? null : mb_convert_case(mb_strtolower($limpio, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    }

    public static function email(?string $valor): ?string
    {
        $limpio = mb_strtolower(trim((string) $valor), 'UTF-8');

        return $limpio === '' ? null : $limpio;
    }

    /** Carnet sin espacios y con el complemento en mayúsculas: "1234567-1a" → "1234567-1A" */
    public static function ci(?string $valor): ?string
    {
        $limpio = mb_strtoupper(preg_replace('/\s+/u', '', (string) $valor), 'UTF-8');

        return $limpio === '' ? null : $limpio;
    }
}
