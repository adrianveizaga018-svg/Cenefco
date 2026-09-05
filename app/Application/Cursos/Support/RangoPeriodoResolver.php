<?php

namespace App\Application\Cursos\Support;

use Carbon\Carbon;

final class RangoPeriodoResolver
{
    public static function resolver(string $periodo, string $fecha, ?string $fechaFin = null): array
    {
        $base = Carbon::parse($fecha);

        return match ($periodo) {
            'dia'   => [$base->copy()->startOfDay(), $base->copy()->endOfDay()],
            'anio'  => [$base->copy()->startOfYear(), $base->copy()->endOfYear()],
            'rango' => [$base->copy()->startOfDay(), Carbon::parse($fechaFin ?? $fecha)->endOfDay()],
            default => [$base->copy()->startOfMonth(), $base->copy()->endOfMonth()],
        };
    }
}
