<?php

namespace App\Domain\Honorarios\Enums;

enum TipoHonorario: string
{
    case DiplomadoFijo = 'diplomado_fijo';
    case RmPorDia       = 'rm_por_dia';
    case AvalPorDia     = 'aval_por_dia';
}
