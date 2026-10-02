<?php

namespace App\Infrastructure\WhatsApp\BotHandlers;

use App\Infrastructure\Shared\Services\WhatsAppService;
use App\Infrastructure\WhatsApp\ConversationManager;
use App\Infrastructure\WhatsApp\Enums\BotButton;
use App\Infrastructure\WhatsApp\Enums\BotState;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InscripcionBotHandler
{
    public function __construct(
        private WhatsAppService $wa,
        private ConversationManager $conv,
        private MenuHandler $menu,
    ) {}

    public function iniciar(string $from, int $programaId): void
    {
        $programa = DB::table('t_programa')
            ->where('id_programa', $programaId)
            ->where('estado', 1)
            ->whereNull('deleted_at')
            ->first(['id_programa', 'nombre_programa', 'id_imp']);

        if (! $programa) {
            $this->wa->sendText($from, '⚠️ Ese programa ya no está disponible.');
            $this->menu->handle($from);

            return;
        }

        $this->conv->setState($from, BotState::INSCRIPCION_NOMBRE->value, [
            'ins_programa_id'     => $programa->id_programa,
            'ins_programa_nombre' => $programa->nombre_programa,
            'ins_id_imp'          => $programa->id_imp,
        ]);

        $this->wa->sendText(
            $from,
            "📝 *Inscripción: {$programa->nombre_programa}*\n\n".
            "Vamos a registrar tu inscripción en pocos pasos. Puedes cancelar en cualquier momento escribiendo *cancelar*.\n\n".
            "📌 *Paso 1 de 3*\n¿Cuál es tu *nombre completo*?"
        );
    }

    public function recibirNombre(string $from, string $texto, array $contexto): void
    {
        $nombre = trim($texto);

        if (strlen($nombre) < 3) {
            $this->wa->sendText($from, '⚠️ Por favor escribe tu nombre completo (mínimo 3 caracteres).');

            return;
        }

        $partes      = preg_split('/\s+/', $nombre, 2);
        $primerNombre = $partes[0];
        $resto        = $partes[1] ?? '';

        $contexto['ins_nombre']    = $primerNombre;
        $contexto['ins_apellidos'] = $resto;

        $this->conv->setState($from, BotState::INSCRIPCION_CI->value, $contexto);

        $this->wa->sendText(
            $from,
            "✅ Nombre registrado: *{$nombre}*\n\n".
            "📌 *Paso 2 de 3*\n¿Cuál es tu *número de CI* (cédula de identidad)?\n\n".
            '_Escribe solo el número, sin puntos ni guiones. Ejemplo: 5821034_'
        );
    }

    public function recibirCI(string $from, string $texto, array $contexto): void
    {
        $ci = trim(preg_replace('/[^0-9A-Za-z]/', '', $texto));

        if (! preg_match('/^\d{5,10}[A-Za-z]?$/', $ci)) {
            $this->wa->sendText($from, '⚠️ El CI no parece válido. Escribe solo los números de tu cédula (5 a 10 dígitos). Ejemplo: 5821034');

            return;
        }

        $contexto['ins_ci'] = $ci;

        $this->conv->setState($from, BotState::INSCRIPCION_EMAIL->value, $contexto);

        $this->wa->sendText(
            $from,
            "✅ CI registrado: *{$ci}*\n\n".
            "📌 *Paso 3 de 3*\n¿Cuál es tu *correo electrónico*?\n\n".
            '_Ejemplo: tucorreo@gmail.com_'
        );
    }

    public function recibirEmail(string $from, string $texto, array $contexto): void
    {
        $email = strtolower(trim($texto));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->wa->sendText($from, '⚠️ El correo electrónico no es válido. Por favor verifica e intenta nuevamente.');

            return;
        }

        $contexto['ins_email'] = $email;

        $this->conv->setState($from, BotState::INSCRIPCION_CONFIRMAR->value, $contexto);

        $nombreCompleto = trim($contexto['ins_nombre'].' '.$contexto['ins_apellidos']);
        $programa       = $contexto['ins_programa_nombre'] ?? 'No especificado';

        $resumen  = "📋 *Resumen de tu inscripción*\n\n";
        $resumen .= "📚 *Programa:* {$programa}\n";
        $resumen .= "👤 *Nombre:* {$nombreCompleto}\n";
        $resumen .= "🪪 *CI:* {$contexto['ins_ci']}\n";
        $resumen .= "✉️ *Email:* {$email}\n";
        $resumen .= "📞 *Teléfono:* {$from}\n\n";
        $resumen .= '¿Los datos son correctos?';

        $this->wa->sendButtons($from, $resumen, [
            ['id' => BotButton::INSCRIPCION_CONFIRMAR->value, 'title' => '✅ Confirmar'],
            ['id' => BotButton::INSCRIPCION_CANCELAR->value,  'title' => '❌ Cancelar'],
        ]);
    }

    public function confirmar(string $from, array $contexto): void
    {

        if (empty($contexto['ins_programa_id']) || empty($contexto['ins_ci'])) {
            Log::warning('[InscripcionBotHandler] Contexto incompleto al confirmar', [
                'phone'   => $from,
                'contexto' => $contexto,
            ]);
            $this->wa->sendText($from, '⚠️ Ocurrió un problema con tu sesión. Por favor inicia el proceso de nuevo.');
            $this->conv->setState($from, BotState::MENU->value, []);
            $this->menu->handle($from);

            return;
        }

        $nombreCompleto = trim($contexto['ins_nombre'].' '.$contexto['ins_apellidos']);
        $apellidos      = explode(' ', $contexto['ins_apellidos'] ?? '', 2);

        try {
            DB::transaction(function () use ($contexto, $from, $apellidos): void {
                $imp = DB::table('t_imparte')
                    ->where('id_prog', $contexto['ins_programa_id'])
                    ->where('estado', 1)
                    ->latest('id_imp')
                    ->first(['id_imp']);

                if (! $imp) {
                    throw new \RuntimeException('El programa ya no está disponible.');
                }

                $usuario = DB::table('t_usuario')
                    ->where('ci', $contexto['ins_ci'])
                    ->first(['id_us']);

                if ($usuario) {
                    $idUs = $usuario->id_us;
                } else {
                    $idUs = DB::table('t_usuario')->insertGetId([
                        'nombre'          => $contexto['ins_nombre'] ?? '',
                        'apellido_paterno'=> $apellidos[0] ?? null,
                        'apellido_materno'=> $apellidos[1] ?? null,
                        'ci'              => $contexto['ins_ci'],
                        'email'           => $contexto['ins_email'] ?? '',
                        'telefono'        => $from,
                        'estado'          => 1,
                        'fecha_reg'       => now()->toDateTimeString(),
                    ], 'id_us');
                }

                DB::table('t_inscripcion')->insertGetId([
                    'id_us_reg'  => $idUs,
                    'id_imp'     => $imp->id_imp,
                    'email'      => $contexto['ins_email'] ?? '',
                    'telefono'   => $from,
                    'origen'     => 'whatsapp',
                    'fecha_ins'  => now()->toDateString(),
                    'fecha_reg'  => now()->toDateTimeString(),
                ], 'id_ins');
            });

            $programa = $contexto['ins_programa_nombre'] ?? 'el programa';
            $email    = $contexto['ins_email'] ?? '';

            $this->wa->sendText(
                $from,
                "🎉 *¡Inscripción registrada exitosamente!*\n\n".
                "Hola *{$nombreCompleto}*, tu inscripción para *{$programa}* fue registrada.\n\n".
                "📧 Recibirás información adicional en: {$email}\n\n".
                '¡Gracias por inscribirte! Nuestro equipo te contactará pronto.'
            );

            $this->conv->setState($from, BotState::MENU->value, []);

            $this->wa->sendButtons($from, '¿En qué más puedo ayudarte?', [
                ['id' => BotButton::PROGRAMAS->value, 'title' => '📚 Ver más programas'],
                ['id' => BotButton::MENU->value,       'title' => '🏠 Menú principal'],
            ]);

            $this->notificarAsesor($from, $nombreCompleto, $contexto['ins_ci'], $programa);

        } catch (\RuntimeException $e) {
            $this->wa->sendText($from, "⚠️ {$e->getMessage()} Por favor elige otro programa.");
            $this->conv->setState($from, BotState::MENU->value, []);
            $this->menu->handle($from);
        } catch (\Throwable $e) {
            Log::error('[InscripcionBotHandler] Error al crear inscripción', [
                'phone' => $from,
                'error' => $e->getMessage(),
            ]);

            $this->wa->sendText(
                $from,
                '⚠️ Ocurrió un error al procesar tu solicitud. Por favor intenta nuevamente o contáctanos directamente.'
            );

            $this->wa->sendButtons($from, '¿Qué deseas hacer?', [
                ['id' => BotButton::SOPORTE->value, 'title' => '📞 Hablar con asesor'],
                ['id' => BotButton::MENU->value,     'title' => '🏠 Menú principal'],
            ]);

            $this->conv->setState($from, BotState::MENU->value, []);
        }
    }

    public function cancelar(string $from): void
    {
        $this->conv->setState($from, BotState::MENU->value, []);
        $this->wa->sendText($from, '❌ Inscripción cancelada. ¿En qué más puedo ayudarte?');
        $this->menu->handle($from);
    }

    public function esCancelacion(string $texto): bool
    {
        $lower = strtolower(trim($texto));

        return in_array($lower, ['cancelar', 'cancel', 'salir', 'exit', 'no', 'menu', 'menú']);
    }

    private function notificarAsesor(string $from, string $nombre, string $ci, string $programa): void
    {
        try {
            $asesor = DB::table('web_asesor')
                ->where('activo', true)
                ->whereNotNull('telefono')
                ->inRandomOrder()
                ->first(['nombre', 'telefono']);

            if (! $asesor) {
                return;
            }

            $this->wa->sendText(
                $asesor->telefono,
                "🔔 *Nueva inscripción vía WhatsApp*\n\n".
                "👤 *Nombre:* {$nombre}\n".
                "🪪 *CI:* {$ci}\n".
                "📚 *Programa:* {$programa}\n".
                "📞 *Teléfono:* {$from}\n\n".
                'Revisa el panel de administración para gestionar esta solicitud.'
            );
        } catch (\Throwable $e) {

            Log::warning('[InscripcionBotHandler] No se pudo notificar al asesor sobre inscripción', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
