<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Infrastructure\Notificaciones\Models\Notificacion;

class LimpiarNotificacionesAntiguas extends Command
{
    protected $signature   = 'notificaciones:limpiar';
    protected $description = 'Elimina notificaciones leídas > 90 días, expiradas > 7 días y no leídas > 180 días';

    public function handle(): int
    {

        $totalLeidas = $this->eliminarConImagenesEnChunks(
            Notificacion::where('leida', true)->where('created_at', '<', now()->subDays(90))
        );
        $this->line("  Leídas antiguas eliminadas: {$totalLeidas}");

        $totalExpiradas = $this->eliminarConImagenesEnChunks(
            Notificacion::whereNotNull('expires_at')->where('expires_at', '<', now()->subDays(7))
        );
        $this->line("  Expiradas eliminadas: {$totalExpiradas}");

        $totalMuyAntiguas = $this->eliminarConImagenesEnChunks(
            Notificacion::where('leida', false)->where('created_at', '<', now()->subDays(180))
        );
        $this->line("  No leídas muy antiguas eliminadas: {$totalMuyAntiguas}");

        $this->info('Limpieza completada.');
        return self::SUCCESS;
    }

    private function eliminarConImagenesEnChunks(\Illuminate\Database\Eloquent\Builder $query): int
    {
        $total = 0;

        $query->chunkById(500, function ($notifs) use (&$total) {
            foreach ($notifs as $notif) {
                if ($notif->imagen_url) {
                    $path = str_replace('/storage/', '', $notif->imagen_url);
                    Storage::disk('public')->delete($path);
                }
                $notif->delete();
                $total++;
            }
        });

        return $total;
    }
}
