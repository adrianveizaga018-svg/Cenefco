<?php

namespace App\Shared\Kernel\Support;

use Illuminate\Support\Facades\DB;

/**
 * Validador centralizado de número de boleta/recibo duplicado.
 * Usado en todos los flujos de pago: Caja, Cobranzas, Inscripciones, Pagos.
 */
final class BoletaValidator
{
    /**
     * Verifica si el número de boleta ya existe en t_pago.
     *
     * @return array{duplicada: bool, message: string, datos: array|null}
     */
    public static function verificar(?string $nroBoleta): array
    {
        // Vacío, nulo o "efectivo" no se validan
        if (! $nroBoleta || strtolower(trim($nroBoleta)) === 'efectivo') {
            return ['duplicada' => false, 'message' => '', 'datos' => null];
        }

        $nroBoleta = trim($nroBoleta);

        $existePago = DB::table('t_pago')
            ->where('nro_boleta_bancaria', $nroBoleta)
            ->where('estado', 1)
            ->orderByDesc('id_pago')
            ->first(['id_pago', 'id_us_reg', 'fecha_reg', 'monto_pagado', 'metodo_pago']);

        if (! $existePago) {
            return ['duplicada' => false, 'message' => '', 'datos' => null];
        }

        // Buscar al cajero (primero en t_usuario, luego en users de Laravel)
        $cajeroNombre = 'cajero desconocido';
        if ($existePago->id_us_reg) {
            $cajeroT = DB::table('t_usuario')
                ->where('id_us', $existePago->id_us_reg)
                ->first(['nombre', 'appaterno']);
            if ($cajeroT) {
                $cajeroNombre = trim(($cajeroT->nombre ?? '') . ' ' . ($cajeroT->appaterno ?? ''));
            } else {
                $cajeroAuth = DB::table('users')
                    ->where('id', $existePago->id_us_reg)
                    ->first(['nombre', 'apellido']);
                if ($cajeroAuth) {
                    $cajeroNombre = trim(($cajeroAuth->nombre ?? '') . ' ' . ($cajeroAuth->apellido ?? ''));
                }
            }
        }

        $fechaRegistro = $existePago->fecha_reg
            ? date('d/m/Y H:i', strtotime((string) $existePago->fecha_reg))
            : 'fecha desconocida';

        $monto = $existePago->monto_pagado
            ? 'Bs. ' . number_format((float) $existePago->monto_pagado, 2)
            : '';

        $message = "El numero de boleta/recibo '{$nroBoleta}' ya fue registrado"
            . ($monto ? " por {$monto}" : '')
            . " el {$fechaRegistro}"
            . " por {$cajeroNombre}."
            . " Verifique que no sea un comprobante duplicado antes de continuar.";

        return [
            'duplicada'      => true,
            'message'        => $message,
            'datos'          => [
                'nro_boleta'     => $nroBoleta,
                'registrado_por' => $cajeroNombre,
                'registrado_el'  => $fechaRegistro,
                'monto'          => $monto,
            ],
        ];
    }
}
