<?php

namespace Tests\Feature\Pagos;

use App\Shared\Kernel\Support\PlanGratuito;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\Support\DatosAcademicos;
use Tests\TestCase;

/** Plan con costo 0: Caja inscribe sin registrar pago y el estudiante puede inscribirse solo. */
class PlanGratuitoTest extends TestCase
{
    use RefreshDatabase, DatosAcademicos;

    public function test_solo_un_costo_cero_explicito_es_gratuito(): void
    {
        $this->assertTrue(PlanGratuito::costoCero('0'));
        $this->assertTrue(PlanGratuito::costoCero('0.00'));
        $this->assertFalse(PlanGratuito::costoCero(null));   // sin precio cargado no es gratis
        $this->assertFalse(PlanGratuito::costoCero(''));
        $this->assertFalse(PlanGratuito::costoCero('1500'));
    }

    public function test_caja_inscribe_sin_pedir_ni_registrar_pago(): void
    {
        [, $idImp] = $this->crearProgramaConVersion();
        $idPlan = $this->crearPlan($idImp, 0);
        Sanctum::actingAs($this->crearCuenta(null, ['inscripciones.crear'], 'Cajero'));

        $this->postJson('/api/v1/caja/inscribir', [
            'id_imp' => $idImp, 'id_plan' => $idPlan,
            'nombre' => 'maría', 'apellido_paterno' => 'gratis', 'ci' => '5556667', 'celular' => '70011122',
        ])->assertCreated()->assertJsonPath('gratuito', true)->assertJsonPath('id_pago', null);

        $this->assertSame(0, DB::table('t_pago')->count());
        $this->assertSame('María', DB::table('t_usuario')->where('ci', '5556667')->value('nombre'));
    }

    public function test_caja_exige_el_pago_en_un_plan_con_costo(): void
    {
        [, $idImp] = $this->crearProgramaConVersion();
        $idPlan = $this->crearPlan($idImp, 1000, [1000]);
        Sanctum::actingAs($this->crearCuenta(null, ['inscripciones.crear'], 'Cajero'));

        $this->postJson('/api/v1/caja/inscribir', ['id_imp' => $idImp, 'id_plan' => $idPlan, 'nombre' => 'x', 'ci' => '5556668'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['monto_pagado', 'fecha_deposito', 'metodo_pago']);
    }

    public function test_el_estudiante_se_inscribe_gratis_y_deja_de_verlo_en_el_catalogo(): void
    {
        $idUs = $this->crearEstudiante('8889990');
        [, $idImp] = $this->crearProgramaConVersion('Taller gratuito');
        $idPlan = $this->crearPlan($idImp, 0);
        Sanctum::actingAs($this->crearCuenta('8889990', ['estudiante.oferta']));

        $this->getJson('/api/v1/estudiante/catalogo')->assertOk()->assertJsonPath('data.0.gratuito', true);
        $this->postJson('/api/v1/estudiante/inscribirse-gratis', ['id_imp' => $idImp, 'id_plan' => $idPlan])->assertCreated();

        $this->assertTrue(DB::table('t_inscripcion')->where('id_us', $idUs)->where('id_imp', $idImp)->where('estado', 1)->exists());
        $this->getJson('/api/v1/estudiante/catalogo')->assertOk()->assertJsonCount(0, 'data');
        $this->postJson('/api/v1/estudiante/inscribirse-gratis', ['id_imp' => $idImp, 'id_plan' => $idPlan])->assertStatus(422);
    }

    public function test_no_se_inscribe_gratis_con_un_plan_de_pago(): void
    {
        $this->crearEstudiante('8889991');
        [, $idImp] = $this->crearProgramaConVersion();
        $idPlan = $this->crearPlan($idImp, 1500, [1500]);
        Sanctum::actingAs($this->crearCuenta('8889991', ['estudiante.oferta']));

        $this->postJson('/api/v1/estudiante/inscribirse-gratis', ['id_imp' => $idImp, 'id_plan' => $idPlan])->assertStatus(422);
        $this->assertSame(0, DB::table('t_inscripcion')->count());
    }
}
