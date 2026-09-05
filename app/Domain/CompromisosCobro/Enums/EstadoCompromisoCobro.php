<?php

namespace App\Domain\CompromisosCobro\Enums;

enum EstadoCompromisoCobro: string
{
    case Pendiente  = 'pendiente';
    case Cumplido   = 'cumplido';
    case Incumplido = 'incumplido';
    case Cancelado  = 'cancelado';
}
