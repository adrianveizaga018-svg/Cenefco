<?php

namespace Tests\Feature\Pagos;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Support\DatosAcademicos;
use Tests\TestCase;

/** La pantalla de Citas de asesoría usa campos que la tabla original no tenía: debe poder crear, listar, editar y borrar. */
class CitasAsesoriaTest extends TestCase
{
    use RefreshDatabase, DatosAcademicos;

    public function test_crea_lista_edita_y_borra_una_cita(): void
    {
        Sanctum::actingAs($this->crearCuenta(null, ['asesorias.ver', 'asesorias.crear', 'asesorias.editar', 'asesorias.eliminar'], 'Admin'));

        $id = $this->postJson('/api/v1/citas-asesoria', [
            'nombres' => 'María', 'apellidos' => 'Quispe', 'email' => 'maria@example.com', 'celular' => '70000000',
            'programa_interes' => 'Diplomado en Auditoría', 'fecha_preferida' => now()->addDay()->toDateString(), 'hora_preferida' => '10:00',
        ])->assertCreated()->assertJsonPath('nombre_solicitante', 'María Quispe')->json('id_cita_asesoria');

        $this->getJson('/api/v1/citas-asesoria')->assertOk()->assertJsonPath('data.0.id_cita_asesoria', $id);
        $this->putJson("/api/v1/citas-asesoria/{$id}", ['estado' => 'atendida', 'observacion' => 'Se envió la información'])
            ->assertOk()->assertJsonPath('estado', 'atendida');
        $this->deleteJson("/api/v1/citas-asesoria/{$id}")->assertNoContent();
        $this->getJson("/api/v1/citas-asesoria/{$id}")->assertNotFound();
    }
}
