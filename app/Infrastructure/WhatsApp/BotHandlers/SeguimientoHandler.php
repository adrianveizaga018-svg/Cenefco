<?php

namespace App\Infrastructure\WhatsApp\BotHandlers;

use App\Infrastructure\Shared\Services\WhatsAppService;
use App\Infrastructure\WhatsApp\ConversationManager;
use App\Infrastructure\WhatsApp\Enums\BotButton;
use App\Infrastructure\WhatsApp\Enums\BotState;
use Illuminate\Support\Facades\DB;

class SeguimientoHandler
{
    public function __construct(
        private WhatsAppService $wa,
        private ConversationManager $conv,
        private MenuHandler $menu,
    ) {}

    public function pedirCI(string $from): void
    {
        $this->wa->sendText($from,
            "🔍 *Consulta de Inscripción*\n\n".
            "Escribe tu *número de CI* para verificar tus inscripciones.\n\n".
            '_Ejemplo: 5821034_'
        );
        $this->conv->setState($from, BotState::CONSULTA_CI->value);
    }

    public function buscarPorCI(string $from, string $texto): void
    {
        $ci = trim(preg_replace('/[^0-9A-Za-z]/', '', $texto));

        if (strlen($ci) < 5) {
            $this->wa->sendText($from, '⚠️ El CI ingresado no parece válido. Por favor escribe tu número de CI sin puntos ni guiones.');
            $this->wa->sendButtons($from, '¿Qué deseas hacer?', [
                ['id' => BotButton::INSCRIPCION->value, 'title' => '🔍 Intentar de nuevo'],
                ['id' => BotButton::MENU->value,        'title' => '🏠 Menú principal'],
            ]);

            return;
        }

        $inscripciones = DB::table('t_inscripcion as ins')
            ->join('t_usuario as us', 'us.id_us', '=', 'ins.id_us_reg')
            ->leftJoin('t_imparte as imp', 'imp.id_imp', '=', 'ins.id_imp')
            ->leftJoin('t_programa as prog', 'prog.id_programa', '=', 'imp.id_prog')
            ->where('us.ci', $ci)
            ->orderByDesc('ins.fecha_reg')
            ->limit(5)
            ->get([
                'ins.id_ins',
                'us.nombre as us_nombre',
                'us.apellido_paterno',
                'us.apellido_materno',
                'ins.fecha_reg',
                'prog.nombre_programa',
            ]);

        if ($inscripciones->isEmpty()) {
            $this->wa->sendText($from,
                "⚠️ No se encontró ninguna inscripción con CI *{$ci}*.\n\n".
                'Verifica que hayas completado el formulario de inscripción en nuestro portal web o por este chat.'
            );
            $this->wa->sendButtons($from, '¿Qué deseas hacer?', [
                ['id' => BotButton::INSCRIPCION->value, 'title' => '📝 Info de inscripción'],
                ['id' => BotButton::SOPORTE->value,     'title' => '📞 Hablar con asesor'],
                ['id' => BotButton::MENU->value,        'title' => '🏠 Menú principal'],
            ]);
            $this->conv->setState($from, BotState::MENU->value);

            return;
        }

        $primera = $inscripciones->first();
        $nombreCompleto = trim("{$primera->us_nombre} {$primera->apellido_paterno} {$primera->apellido_materno}");

        $texto = "✅ *Inscripción(es) encontrada(s)*\n\n";
        $texto .= "👤 *Nombre:* {$nombreCompleto}\n\n";

        foreach ($inscripciones as $i => $ins) {
            $fecha    = $ins->fecha_reg ? date('d/m/Y', strtotime($ins->fecha_reg)) : '-';
            $programa = $ins->nombre_programa ?? '_(programa eliminado)_';

            $texto .= ($i + 1).". 📚 *{$programa}*\n";
            $texto .= "   📅 Inscrito: {$fecha}\n\n";
        }

        $texto .= 'Para más información contáctanos con nuestro equipo de atención.';

        $this->wa->sendText($from, $texto);

        $this->wa->sendButtons($from, '¿Necesitas algo más?', [
            ['id' => BotButton::SOPORTE->value, 'title' => '📞 Hablar con asesor'],
            ['id' => BotButton::MENU->value,    'title' => '🏠 Menú principal'],
        ]);

        $this->conv->setState($from, BotState::MENU->value);
    }
}
