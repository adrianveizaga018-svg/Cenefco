<?php

namespace Tests\Feature\Pagos;

use App\Infrastructure\Usuarios\Models\User;
use App\Notifications\ComprobanteRevisadoNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\Support\DatosAcademicos;
use Tests\TestCase;

/** El estudiante paga con comprobante desde su panel y Caja lo aprueba o rechaza. */
class ComprobantesEstudianteTest extends TestCase
{
    use RefreshDatabase, DatosAcademicos;

    private int $idUs;
    private int $idImp;
    private int $idIns;
    private User $estudiante;
    private User $cajero;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        Storage::fake('public');

        $this->idUs = $this->crearEstudiante('7654321');
        [, $this->idImp] = $this->crearProgramaConVersion();
        $this->idIns = $this->inscribir($this->idUs, $this->idImp, $this->crearPlan($this->idImp, 1000, [400, 600]));
        $this->estudiante = $this->crearCuenta('7654321', ['estudiante.pagos']);
        $this->cajero = $this->crearCuenta(null, ['inscripciones.crear'], 'Cajero');
    }

    private function datosComprobante(array $extra = []): array
    {
        return $extra + [
            'monto' => 400, 'fecha_deposito' => now()->toDateString(), 'metodo_pago' => 'qr',
            'comprobante' => UploadedFile::fake()->image('comprobante.jpg'),
        ];
    }

    private function enviarComprobante(array $extra = []): int
    {
        Sanctum::actingAs($this->estudiante);
        $this->postJson('/api/v1/estudiante/comprobantes', $this->datosComprobante(['id_ins' => $this->idIns] + $extra))->assertCreated();

        return (int) DB::table('t_pago')->where('id_ins', $this->idIns)->max('id_pago');
    }

    public function test_el_comprobante_queda_en_revision_sin_descontar_deuda(): void
    {
        $this->enviarComprobante();

        Sanctum::actingAs($this->estudiante);
        $this->getJson("/api/v1/estudiante/pago-info/{$this->idIns}")
            ->assertOk()
            ->assertJsonPath('saldo', 1000)
            ->assertJsonPath('en_revision', 1);
    }

    public function test_caja_aprueba_y_la_deuda_baja_con_aviso_al_estudiante(): void
    {
        $idPago = $this->enviarComprobante();

        Sanctum::actingAs($this->cajero);
        $this->getJson('/api/v1/caja/comprobantes')->assertOk()->assertJsonPath('pendientes', 1);
        $this->patchJson("/api/v1/caja/comprobantes/{$idPago}/aprobar")->assertOk()->assertJsonPath('saldo', 600);

        $this->assertEquals(600.0, $this->saldo($this->idIns));
        Notification::assertSentOnDemand(ComprobanteRevisadoNotification::class, fn ($n) => $n->aprobado);
    }

    public function test_caja_rechaza_con_motivo_y_la_deuda_no_cambia(): void
    {
        $idPago = $this->enviarComprobante();

        Sanctum::actingAs($this->cajero);
        $this->patchJson("/api/v1/caja/comprobantes/{$idPago}/rechazar", ['motivo' => 'La foto está borrosa'])->assertOk();
        $this->patchJson("/api/v1/caja/comprobantes/{$idPago}/aprobar")->assertStatus(422);

        $this->assertEquals(1000.0, $this->saldo($this->idIns));
        Notification::assertSentOnDemand(ComprobanteRevisadoNotification::class, fn ($n) => ! $n->aprobado && $n->motivo === 'La foto está borrosa');
    }

    public function test_no_acepta_un_monto_mayor_al_saldo(): void
    {
        Sanctum::actingAs($this->estudiante);
        $this->postJson('/api/v1/estudiante/comprobantes', $this->datosComprobante(['id_ins' => $this->idIns, 'monto' => 5000]))
            ->assertStatus(422);
    }

    public function test_no_puede_pagar_la_inscripcion_de_otro_estudiante(): void
    {
        $otro = $this->crearEstudiante('1111111', 'Luis');
        $insAjena = $this->inscribir($otro, $this->idImp, $this->crearPlan($this->idImp, 500, [500]));

        Sanctum::actingAs($this->estudiante);
        $this->postJson('/api/v1/estudiante/comprobantes', $this->datosComprobante(['id_ins' => $insAjena]))->assertStatus(422);
        $this->getJson("/api/v1/estudiante/pago-info/{$insAjena}")->assertNotFound();
    }

    public function test_un_estudiante_no_entra_a_la_bandeja_de_caja(): void
    {
        Sanctum::actingAs($this->estudiante);
        $this->getJson('/api/v1/caja/comprobantes')->assertForbidden();
    }

    public function test_preinscripcion_del_portal_elige_plan_y_paga(): void
    {
        $preinscripcion = $this->inscribir($this->idUs, $this->crearProgramaConVersion('Otro diplomado')[1], null, 'portal');
        $idImp = DB::table('t_inscripcion')->where('id_ins', $preinscripcion)->value('id_imp');
        $idPlan = $this->crearPlan($idImp, 900, [300, 600]);

        Sanctum::actingAs($this->estudiante);
        $this->getJson("/api/v1/estudiante/pago-info/{$preinscripcion}")->assertOk()->assertJsonPath('requiere_plan', true);
        $this->postJson('/api/v1/estudiante/comprobantes', $this->datosComprobante(['id_ins' => $preinscripcion, 'monto' => 300]))
            ->assertStatus(422); // sin plan elegido
        $this->postJson('/api/v1/estudiante/comprobantes', $this->datosComprobante(['id_ins' => $preinscripcion, 'monto' => 300, 'id_plan' => $idPlan]))
            ->assertCreated();

        $this->assertEquals($idPlan, DB::table('t_inscripcion')->where('id_ins', $preinscripcion)->value('id_plan'));
        $this->assertEquals(900.0, $this->saldo($preinscripcion));
        $this->postJson("/api/v1/estudiante/inscripciones/{$preinscripcion}/plan", ['id_plan' => $idPlan])->assertStatus(422);
    }

    public function test_se_inscribe_a_un_programa_de_pago_con_el_primer_comprobante(): void
    {
        [, $idImp] = $this->crearProgramaConVersion('Diplomado nuevo');
        $idPlan = $this->crearPlan($idImp, 1200, [200, 1000]);

        Sanctum::actingAs($this->estudiante);
        $res = $this->postJson('/api/v1/estudiante/inscribirse', $this->datosComprobante(['id_imp' => $idImp, 'id_plan' => $idPlan, 'monto' => 200]))
            ->assertCreated();

        $idIns = $res->json('id_ins');
        $this->assertEquals(1200.0, $this->saldo($idIns));
        $this->postJson('/api/v1/estudiante/inscribirse', $this->datosComprobante(['id_imp' => $idImp, 'id_plan' => $idPlan, 'monto' => 200]))
            ->assertStatus(422); // ya inscrito
    }

    public function test_programa_con_formulario_propio_no_se_inscribe_desde_el_panel(): void
    {
        [, $idImp] = $this->crearProgramaConVersion('Con formulario', formularioId: $this->crearFormulario());
        $idPlan = $this->crearPlan($idImp, 0);

        Sanctum::actingAs($this->estudiante);
        $this->postJson('/api/v1/estudiante/inscribirse-gratis', ['id_imp' => $idImp, 'id_plan' => $idPlan])->assertStatus(422);
    }
}
