<?php

namespace Tests\Feature\Pagos;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\Support\DatosAcademicos;
use Tests\TestCase;

/** Huecos encontrados en la revisión previa a producción: que no vuelvan a abrirse. */
class SeguridadRutasTest extends TestCase
{
    use RefreshDatabase, DatosAcademicos;

    public function test_un_estudiante_no_modifica_autoridades_organigramas_ni_sueldos_docentes(): void
    {
        Sanctum::actingAs($this->crearCuenta('5551234', ['estudiante.dashboard']));

        $this->postJson('/api/v1/autoridades', ['nombre' => 'Intruso'])->assertForbidden();
        $this->deleteJson('/api/v1/autoridades/1')->assertForbidden();
        $this->postJson('/api/v1/organigramas', [])->assertForbidden();
        $this->putJson('/api/v1/sueldos-docentes/1', ['monto_total' => 99999])->assertForbidden();
        $this->postJson('/api/v1/sueldos-docentes/1', ['monto_total' => 99999])->assertForbidden();
    }

    public function test_la_subida_publica_guarda_con_la_extension_real_y_no_la_del_nombre(): void
    {
        Storage::fake('public');
        // Un PDF de verdad, pero llamado .php
        $falso = UploadedFile::fake()->createWithContent('ataque.php', "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF");

        $resp = $this->post('/api/v1/public/upload/file', ['file' => $falso], ['Accept' => 'application/json']);

        // Se rechaza en la validación o se guarda con la extensión real: nunca queda un .php en la carpeta pública
        $this->assertContains($resp->status(), [201, 422]);
        if ($resp->status() === 201) {
            $this->assertStringEndsWith('.pdf', $resp->json('url'));
        }
        $this->assertEmpty(array_filter(Storage::disk('public')->allFiles(), fn ($f) => str_ends_with($f, '.php')));
    }

    public function test_el_webhook_de_certificados_sin_clave_configurada_no_acepta_nada(): void
    {
        config(['certificados_externos.webhook_secret' => '']);
        $this->postJson('/api/v1/certificados/webhook-lote', ['lote_id' => 'x', 'curso' => 'y'])->assertUnauthorized();
    }

    public function test_un_id_no_numerico_responde_404_y_no_500(): void
    {
        $this->getJson('/api/v1/public/cursos/abc')->assertNotFound();
        $this->getJson('/api/v1/portal/noticias/abc')->assertNotFound();
    }
}
