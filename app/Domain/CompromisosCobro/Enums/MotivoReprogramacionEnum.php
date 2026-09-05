<?php

namespace App\Domain\CompromisosCobro\Enums;

enum MotivoReprogramacionEnum: string
{
    case PidioMasTiempo      = 'pidio_mas_tiempo';
    case NoRespondio         = 'no_respondio';
    case PrometioPagarPronto = 'promete_pagar_pronto';
    case Otro                = 'otro';
}
