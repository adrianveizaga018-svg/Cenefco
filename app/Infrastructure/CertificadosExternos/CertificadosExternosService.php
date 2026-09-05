<?php

namespace App\Infrastructure\CertificadosExternos;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class CertificadosExternosService
{
    private function baseUrl(): string
    {
        return rtrim((string) config('certificados_externos.base_url'), '/');
    }

    private function cliente(): PendingRequest
    {
        return Http::withToken((string) config('certificados_externos.token'))
            ->acceptJson()
            ->timeout(10);
    }

    public function listarCursos(?string $query = null): Response
    {
        return $this->cliente()->get("{$this->baseUrl()}/cursos/", array_filter([
            'q' => $query,
        ]));
    }

    public function registrarEstudiante(int $cursoId, string $nombre): Response
    {
        return $this->cliente()->post("{$this->baseUrl()}/cursos/{$cursoId}/estudiantes/", [
            'nombre' => $nombre,
        ]);
    }

    public function registrarEstudiantes(int $cursoId, array $nombres): Response
    {
        return $this->cliente()->post("{$this->baseUrl()}/cursos/{$cursoId}/estudiantes/", [
            'nombres' => $nombres,
        ]);
    }
}
