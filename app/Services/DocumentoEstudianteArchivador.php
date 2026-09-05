<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class DocumentoEstudianteArchivador
{
    public function archivar(?string $urlTemporal, string $ci, string $tipoInscripcion, int $programaId, string $campo): ?string
    {
        if (! $urlTemporal) {
            return $urlTemporal;
        }

        $rutaRelativa = $this->rutaRelativaDesdeUrl($urlTemporal);
        if (! $rutaRelativa || ! Storage::disk('public')->exists($rutaRelativa)) {
            return $urlTemporal;
        }

        if (str_starts_with($rutaRelativa, 'uploads/estudiantes/')) {
            return $urlTemporal;
        }

        $extension  = pathinfo($rutaRelativa, PATHINFO_EXTENSION) ?: 'bin';
        $ciSeguro   = preg_replace('/[^A-Za-z0-9_-]/', '', $ci) ?: 'sin-ci';
        $campoSeguro = preg_replace('/[^A-Za-z0-9_-]/', '_', $campo) ?: 'documento';

        $rutaDestino = "uploads/estudiantes/{$ciSeguro}/{$tipoInscripcion}/{$programaId}/{$campoSeguro}.{$extension}";

        try {
            if (Storage::disk('public')->exists($rutaDestino)) {
                $rutaDestino = "uploads/estudiantes/{$ciSeguro}/{$tipoInscripcion}/{$programaId}/{$campo}-" . now()->format('YmdHis') . ".{$extension}";
            }

            $contenido = Storage::disk('public')->get($rutaRelativa);
            Storage::disk('public')->put($rutaDestino, $contenido);

            if (Storage::disk('public')->exists($rutaDestino)) {
                Storage::disk('public')->delete($rutaRelativa);
                return '/storage/' . $rutaDestino;
            }

            return $urlTemporal;
        } catch (\Throwable $e) {
            Log::warning('No se pudo archivar documento de estudiante, se conserva la ubicación temporal', [
                'url_temporal' => $urlTemporal,
                'ci'           => $ci,
                'error'        => $e->getMessage(),
            ]);

            return $urlTemporal;
        }
    }

    private function rutaRelativaDesdeUrl(string $url): ?string
    {
        $prefijo = '/storage/';
        if (! str_starts_with($url, $prefijo)) {
            return null;
        }

        return substr($url, strlen($prefijo));
    }
}
