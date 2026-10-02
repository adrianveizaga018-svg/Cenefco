<?php

namespace App\Enums;

enum DestinatarioEnum: string
{
    case USUARIO = 'usuario';
    case ROL     = 'rol';
    case TODOS   = 'todos';
}
