<?php

namespace App\Enums;

enum PrioridadEnum: string
{
    case BAJA    = 'baja';
    case MEDIA   = 'media';
    case ALTA    = 'alta';
    case CRITICA = 'critica';
}
