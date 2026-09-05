<?php

namespace App\Infrastructure\WhatsApp\BotHandlers;

use App\Infrastructure\Shared\Services\WhatsAppService;
use App\Infrastructure\WhatsApp\ConversationManager;
use App\Infrastructure\WhatsApp\Enums\BotButton;
use App\Infrastructure\WhatsApp\Enums\BotState;
use Illuminate\Support\Facades\DB;

class DocenteHandler
{
    public function __construct(
        private WhatsAppService $wa,
        private ConversationManager $conv,
        private MenuHandler $menu
    ) {}

    public function showLista(string $from): void
    {
        $docentes = DB::table('web_docente_perfil')
            ->whereNull('deleted_at')
            ->where('mostrar_en_web', 1)
            ->where('estado', 'publicado')
            ->orderBy('orden')
            ->limit(10)
            ->get(['id', 'nombre_completo', 'titulo_academico', 'especialidad']);

        if ($docentes->isEmpty()) {
            $this->wa->sendText($from, '😔 No hay docentes registrados en este momento.');
            $this->menu->handle($from);

            return;
        }

        $rows = $docentes->map(fn ($d) => [
            'id' => BotButton::PREFIX_DOCENTE.$d->id,
            'title' => mb_substr($d->nombre_completo ?? '', 0, 24),
            'description' => mb_substr($d->especialidad ?? $d->titulo_academico ?? '', 0, 72),
        ])->all();

        $this->wa->sendList(
            $from,
            '👨‍🏫 Nuestros Docentes',
            'Selecciona un docente para ver su perfil académico.',
            config('bot.cenefco.nombre'),
            'Ver docentes',
            [['title' => '👨‍🏫 Planta Docente', 'rows' => $rows]]
        );

        $this->conv->setState($from, BotState::DOCENTES->value);
    }

    public function showDetalle(string $from, int $docenteId): void
    {
        $docente = DB::table('web_docente_perfil')
            ->where('id', $docenteId)
            ->whereNull('deleted_at')
            ->first();

        if (! $docente) {
            $this->wa->sendText($from, '⚠️ Docente no encontrado.');
            $this->showLista($from);

            return;
        }

        $texto = "👨‍🏫 *{$docente->nombre_completo}*\n";

        if ($docente->titulo_academico) {
            $texto .= "🎓 {$docente->titulo_academico}\n";
        }

        if ($docente->especialidad) {
            $texto .= "🔬 *Especialidad:* {$docente->especialidad}\n";
        }

        if ($docente->email_publico) {
            $texto .= "✉️ {$docente->email_publico}\n";
        }

        if ($docente->biografia) {
            $texto .= "\n".mb_substr($docente->biografia, 0, 400);
        }

        $imageUrl = null;
        if ($docente->foto_url) {
            $raw = $docente->foto_url;
            $imageUrl = str_starts_with($raw, 'http')
                ? $raw
                : rtrim(config('app.url'), '/').'/'.ltrim($raw, '/');
        }

        if ($imageUrl) {
            try {
                $this->wa->sendButtonsWithImage($from, $imageUrl, $texto, [
                    ['id' => BotButton::DOCENTES->value, 'title' => '👨‍🏫 Ver más docentes'],
                ], 'Docente CENEFCO');
            } catch (\Throwable) {
                $this->wa->sendText($from, $texto);
                $this->wa->sendButtons($from, '¿Qué deseas hacer?', [
                    ['id' => BotButton::DOCENTES->value, 'title' => '👨‍🏫 Ver más docentes'],
                    ['id' => BotButton::MENU->value,      'title' => '🏠 Menú principal'],
                ]);
            }
        } else {
            $this->wa->sendText($from, $texto);
            $this->wa->sendButtons($from, '¿Qué deseas hacer?', [
                ['id' => BotButton::DOCENTES->value, 'title' => '👨‍🏫 Ver más docentes'],
                ['id' => BotButton::MENU->value,      'title' => '🏠 Menú principal'],
            ]);
        }

        $this->conv->setState($from, BotState::DOCENTE_DETALLE->value, ['docente_id' => $docenteId]);
    }
}
