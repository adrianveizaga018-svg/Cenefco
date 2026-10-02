<?php

namespace App\Infrastructure\WhatsApp\Enums;

enum BotButton: string
{
    case MENU = 'btn_menu';
    case PROGRAMAS = 'btn_programas';
    case EVENTOS = 'btn_eventos';
    case DOCENTES = 'btn_docentes';
    case FAQS = 'btn_faqs';
    case HORARIO = 'btn_horario';
    case UBICACION = 'btn_ubicacion';
    case SOPORTE = 'btn_soporte';
    case INSCRIPCION = 'btn_inscripcion';

    case INSCRIPCION_CONFIRMAR = 'ins_confirmar';
    case INSCRIPCION_CANCELAR = 'ins_cancelar';

    const PREFIX_PROGRAMA = 'prog_';

    const PREFIX_EVENTO = 'evento_';

    const PREFIX_DOCENTE = 'doc_';

    const PREFIX_INSCRIBIR = 'inscribir_';
}
