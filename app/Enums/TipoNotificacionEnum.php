<?php

namespace App\Enums;

enum TipoNotificacionEnum: string
{
    case BIENVENIDA            = 'bienvenida';
    case PAGO_VENCIDO          = 'pago_vencido';
    case CUOTA_PROXIMA         = 'cuota_proxima';
    case INSCRIPCION_REGISTRADA = 'inscripcion_registrada';
    case NOTA_PUBLICADA        = 'nota_publicada';
    case CERTIFICADO_LISTO     = 'certificado_listo';
    case COMUNICADO            = 'comunicado';
    case ACTIVIDAD             = 'actividad';
    case STOCK_BAJO            = 'stock_bajo';
    case APROBACION_PENDIENTE  = 'aprobacion_pendiente';
    case PAGO_REGISTRADO       = 'pago_registrado';
    case PAGO_OBSERVADO        = 'pago_observado';
    case COMPROMISO_COBRO         = 'compromiso_cobro';
    case COMPROMISO_COBRO_VENCIDO = 'compromiso_cobro_vencido';
}
