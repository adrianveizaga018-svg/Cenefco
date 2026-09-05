<?php

namespace App\Infrastructure\WhatsApp;

use App\Infrastructure\WhatsApp\Enums\BotState;
use App\Infrastructure\WhatsApp\Models\WhatsappConversacion;
use App\Infrastructure\WhatsApp\Models\WhatsappMensaje;
use Illuminate\Support\Facades\Log;

class ConversationManager
{
    public function getOrCreate(string $phone, ?string $nombre = null): WhatsappConversacion
    {
        $conv = WhatsappConversacion::where('phone', $phone)->first();

        if (! $conv) {
            $conv = WhatsappConversacion::create([
                'phone' => $phone,
                'nombre' => $nombre,
                'estado' => BotState::MENU->value,
                'contexto' => [],
            ]);
        } elseif ($nombre && $conv->nombre !== $nombre) {
            $conv->update(['nombre' => $nombre]);
            $conv->nombre = $nombre;
        }

        return $conv;
    }

    public function setState(string $phone, string $estado, array $contexto = []): void
    {
        WhatsappConversacion::where('phone', $phone)->update([
            'estado' => $estado,
            'contexto' => $contexto,
        ]);
    }

    public function setContexto(string $phone, array $contexto): void
    {
        WhatsappConversacion::where('phone', $phone)->update([
            'contexto' => $contexto,
        ]);
    }

    public function linkCliente(string $phone, int $clienteId): void
    {
        WhatsappConversacion::where('phone', $phone)->update([
            'cliente_id' => $clienteId,
        ]);
    }

    public function reset(string $phone): void
    {
        $this->setState($phone, BotState::MENU->value, []);
    }

    public function logMensaje(
        WhatsappConversacion $conversacion,
        string $direccion,
        string $tipo,
        ?string $contenido,
        ?string $waMessageId = null
    ): void {
        try {
            WhatsappMensaje::create([
                'conversacion_id' => $conversacion->id,
                'phone' => $conversacion->phone,
                'direccion' => $direccion,
                'tipo' => $tipo,
                'contenido' => $contenido,
                'whatsapp_message_id' => $waMessageId,
            ]);
        } catch (\Throwable $e) {
            Log::warning('[ConversationManager] No se pudo registrar mensaje entrante', [
                'phone' => $conversacion->phone,
                'tipo' => $tipo,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function getUltimosMensajes(string $phone, int $limit = 10): array
    {
        $conv = WhatsappConversacion::where('phone', $phone)->first();
        if (! $conv) {
            return [];
        }

        return WhatsappMensaje::where('conversacion_id', $conv->id)
            ->whereIn('tipo', ['text', 'texto'])
            ->whereNotNull('contenido')
            ->orderByDesc('id')
            ->skip(1)
            ->take($limit)
            ->get(['direccion', 'contenido'])
            ->reverse()     
            ->values()
            ->map(fn ($m) => ['direccion' => $m->direccion, 'contenido' => $m->contenido])
            ->toArray();
    }

    public function logSaliente(string $phone, string $tipo, ?string $contenido): void
    {
        try {
            $conv = WhatsappConversacion::where('phone', $phone)->first();
            WhatsappMensaje::create([
                'conversacion_id' => $conv?->id,
                'phone' => $phone,
                'direccion' => 'saliente',
                'tipo' => $tipo,
                'contenido' => $contenido,
            ]);
        } catch (\Throwable $e) {
            Log::warning('[ConversationManager] No se pudo registrar mensaje saliente', [
                'phone' => $phone,
                'tipo' => $tipo,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
