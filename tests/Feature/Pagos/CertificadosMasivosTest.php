<?php

namespace Tests\Feature\Pagos;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Support\DatosAcademicos;
use Tests\TestCase;

/** Certificados masivos: solo quienes no deben nada según el cronograma (el mismo de Caja y la ficha). */
class CertificadosMasivosTest extends TestCase
{
    use RefreshDatabase, DatosAcademicos;

    public function test_incluye_al_que_pago_completo_y_excluye_al_que_debe(): void
    {
        [$idProg, $idImp] = $this->crearProgramaConVersion();
        $idPlan = $this->crearPlan($idImp, 1000, [400, 600]);

        $completo = $this->crearEstudiante('100', 'Completo');
        $this->pagar($this->inscribir($completo, $idImp, $idPlan), $completo, 1000); // pago de Caja, sin id_fechapago

        $debe = $this->crearEstudiante('200', 'Debe');
        $this->pagar($this->inscribir($debe, $idImp, $idPlan), $debe, 400);

        Sanctum::actingAs($this->crearCuenta(null, ['certificados.crear'], 'Certificados'));
        $res = $this->getJson("/api/v1/certificados/preview-pagos-completos?programa_id={$idProg}")->assertOk();

        $this->assertSame(1, $res->json('total'));
        $this->assertSame($completo, $res->json('estudiantes.0.id_us'));
    }

    public function test_en_un_programa_gratuito_todos_los_inscritos_califican(): void
    {
        [$idProg, $idImp] = $this->crearProgramaConVersion();
        $idPlan = $this->crearPlan($idImp, 0);
        $this->inscribir($this->crearEstudiante('300', 'Gratis'), $idImp, $idPlan);

        Sanctum::actingAs($this->crearCuenta(null, ['certificados.crear'], 'Certificados'));

        $this->getJson("/api/v1/certificados/preview-pagos-completos?programa_id={$idProg}")
            ->assertOk()
            ->assertJsonPath('total', 1);
    }
}
