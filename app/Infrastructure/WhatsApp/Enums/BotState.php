<?php

namespace App\Infrastructure\WhatsApp\Enums;

enum BotState: string
{
    case MENU = 'menu';
    case SOPORTE = 'soporte';
    case PROGRAMAS_LISTA = 'programas_lista';
    case PROGRAMA_DETALLE = 'programa_detalle';
    case EVENTOS = 'eventos';
    case EVENTO_DETALLE = 'evento_detalle';
    case DOCENTES = 'docentes';
    case DOCENTE_DETALLE = 'docente_detalle';
    case CONSULTA_CI = 'consulta_ci';

    case INSCRIPCION_NOMBRE = 'inscripcion_nombre';
    case INSCRIPCION_CI = 'inscripcion_ci';
    case INSCRIPCION_EMAIL = 'inscripcion_email';
    case INSCRIPCION_CONFIRMAR = 'inscripcion_confirmar';
}
