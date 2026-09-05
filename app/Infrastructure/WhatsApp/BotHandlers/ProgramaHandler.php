<?php

namespace App\Infrastructure\WhatsApp\BotHandlers;

use App\Infrastructure\Shared\Services\WhatsAppService;
use App\Infrastructure\WhatsApp\ConversationManager;
use App\Infrastructure\WhatsApp\Enums\BotButton;
use App\Infrastructure\WhatsApp\Enums\BotState;
use Illuminate\Support\Facades\DB;

class ProgramaHandler
{
    public function __construct(
        private WhatsAppService $wa,
        private ConversationManager $conv,
        private MenuHandler $menu
    ) {}

    public function showLista(string $from): void
    {
        $programas = DB::table('t_programa')
            ->leftJoin('t_tipoprograma', 't_programa.id_tipoprograma', '=', 't_tipoprograma.id_tipoprograma')
            ->where('t_programa.estado', 1)
            ->whereNull('t_programa.deleted_at')
            ->orderBy('t_programa.orden')
            ->limit(10)
            ->get(['t_programa.id_programa', 't_programa.nombre_programa', 't_tipoprograma.nombre_tipoprograma']);

        if ($programas->isEmpty()) {
            $this->wa->sendText($from, '😔 No hay programas disponibles en este momento.');
            $this->menu->handle($from);

            return;
        }

        $rows = $programas->map(fn ($p) => [
            'id' => BotButton::PREFIX_PROGRAMA.$p->id_programa,
            'title' => mb_substr($p->nombre_programa ?? '', 0, 24),
            'description' => mb_substr($p->nombre_tipoprograma ?? '', 0, 72),
        ])->all();

        $this->wa->sendList(
            $from,
            '📚 Oferta Académica',
            'Selecciona un programa para ver información detallada.',
            config('bot.cenefco.nombre'),
            'Ver programas',
            [['title' => '📚 Programas disponibles', 'rows' => $rows]]
        );

        $this->conv->setState($from, BotState::PROGRAMAS_LISTA->value);
    }

    public function showDetalle(string $from, int $programaId): void
    {
        $programa = DB::table('t_programa')
            ->leftJoin('t_tipoprograma', 't_programa.id_tipoprograma', '=', 't_tipoprograma.id_tipoprograma')
            ->where('t_programa.id_programa', $programaId)
            ->first([
                't_programa.id_programa',
                't_programa.nombre_programa',
                't_programa.descripcion',
                't_programa.inicio_actividades',
                't_programa.finalizacion_actividades',
                't_programa.inicio_inscripciones',
                't_programa.requisitos',
                't_programa.inversion',
                't_programa.dirigido',
                't_programa.url_whatsapp',
                't_programa.foto',
                't_programa.documento1',
                't_programa.titulo_documento1',
                't_tipoprograma.nombre_tipoprograma',
            ]);

        if (! $programa) {
            $this->wa->sendText($from, '⚠️ Programa no encontrado.');
            $this->showLista($from);

            return;
        }

        $texto = "📚 *{$programa->nombre_programa}*\n";
        $texto .= "🏷️ {$programa->nombre_tipoprograma}\n\n";

        $baseUrl = rtrim(config('app.asset_url') ?: config('app.url'), '/');

        if ($programa->foto) {
            $imageUrl = str_starts_with($programa->foto, 'http')
                ? $programa->foto
                : $baseUrl.$programa->foto;
            $this->wa->sendImage($from, $imageUrl, "🖼️ {$programa->nombre_programa}");
        }

        if ($programa->descripcion) {
            $texto .= mb_substr($programa->descripcion, 0, 300)."\n\n";
        }

        if ($programa->inicio_actividades && $programa->inicio_actividades !== '2000-01-01') {
            $texto .= '📅 *Inicio:* '.date('d/m/Y', strtotime($programa->inicio_actividades))."\n";
        }
        if ($programa->finalizacion_actividades && $programa->finalizacion_actividades !== '2000-01-01') {
            $texto .= '📅 *Fin:* '.date('d/m/Y', strtotime($programa->finalizacion_actividades))."\n";
        }
        if ($programa->inicio_inscripciones && $programa->inicio_inscripciones !== '2000-01-01') {
            $texto .= '📋 *Inscripciones desde:* '.date('d/m/Y', strtotime($programa->inicio_inscripciones))."\n";
        }

        $this->wa->sendText($from, $texto);

        if ($programa->dirigido) {
            $this->wa->sendText($from, "🎯 *Dirigido a:*\n".mb_substr($programa->dirigido, 0, 500));
        }

        if ($programa->inversion) {
            $this->wa->sendText($from, "💰 *Inversión:*\n".mb_substr($programa->inversion, 0, 300));
        }

        if ($programa->requisitos) {
            $this->wa->sendText($from, "📎 *Requisitos:*\n".mb_substr($programa->requisitos, 0, 500));
        }

        if ($programa->documento1) {
            $docUrl = str_starts_with($programa->documento1, 'http')
                ? $programa->documento1
                : $baseUrl.$programa->documento1;
            $titulo = $programa->titulo_documento1 ?: 'Documento informativo';
            $this->wa->sendDocument($from, $docUrl, "📄 {$titulo}", "{$programa->nombre_programa}.pdf");
        }

        $buttons = [
            ['id' => BotButton::PREFIX_INSCRIBIR.$programaId, 'title' => '📝 Inscribirme'],
            ['id' => BotButton::PROGRAMAS->value,             'title' => '📚 Ver más programas'],
            ['id' => BotButton::MENU->value,                  'title' => '🏠 Menú principal'],
        ];

        $this->wa->sendButtons($from, '¿Qué deseas hacer?', $buttons);

        $this->conv->setState($from, BotState::PROGRAMA_DETALLE->value, ['programa_id' => $programaId]);
    }
}
