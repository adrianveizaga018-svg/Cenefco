<?php

namespace Tests\Feature\Pagos;

use App\Application\Inscripciones\Services\InscripcionCuotasService;
use App\Domain\Inscripciones\Contracts\InscripcionRepositoryInterface;
use App\Domain\Pagos\Contracts\PagoRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Support\DatosAcademicos;
use Tests\TestCase;

/** El cronograma de cada inscripción es la única fuente de la deuda. */
class CuotasInscripcionTest extends TestCase
{
    use RefreshDatabase, DatosAcademicos;

    private InscripcionCuotasService $cuotas;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cuotas = app(InscripcionCuotasService::class);
    }

    public function test_reparte_los_pagos_en_orden_y_deja_la_cuota_parcial(): void
    {
        $idUs = $this->crearEstudiante();
        [, $idImp] = $this->crearProgramaConVersion();
        $idPlan = $this->crearPlan($idImp, 1900, [300, 800, 800]);
        $idIns = $this->inscribir($idUs, $idImp, $idPlan);

        $this->pagar($idIns, $idUs, 300);
        $this->pagar($idIns, $idUs, 500);
        $res = $this->cuotas->sincronizar($idIns);

        $estados = DB::table('t_inscripcion_cuotas')->where('id_ins', $idIns)->orderBy('nro_cuota')->pluck('estado')->all();
        $this->assertSame(['pagado', 'parcial', 'pendiente'], $estados);
        $this->assertEquals(1100.0, $res->pendiente);
        $this->assertEquals(800.0, $res->total_pagado);
    }

    public function test_los_descuentos_cuentan_como_pagado(): void
    {
        $idUs = $this->crearEstudiante();
        [, $idImp] = $this->crearProgramaConVersion();
        $idIns = $this->inscribir($idUs, $idImp, $this->crearPlan($idImp, 1000, [1000]));

        $this->pagar($idIns, $idUs, 900, ['monto_descuento_extra' => 100]);

        $this->assertEquals(0.0, $this->cuotas->sincronizar($idIns)->pendiente);
    }

    public function test_un_comprobante_del_estudiante_no_descuenta_hasta_aprobarse(): void
    {
        $idUs = $this->crearEstudiante();
        [, $idImp] = $this->crearProgramaConVersion();
        $idIns = $this->inscribir($idUs, $idImp, $this->crearPlan($idImp, 1000, [500, 500]));

        $idPago = $this->pagar($idIns, $idUs, 500, ['estado_verificacion' => InscripcionCuotasService::COMPROBANTE_ESTUDIANTE]);
        $this->assertEquals(1000.0, $this->cuotas->sincronizar($idIns)->pendiente);

        DB::table('t_pago')->where('id_pago', $idPago)->update(['estado_verificacion' => 'verificado']);
        $this->assertEquals(500.0, $this->cuotas->sincronizar($idIns)->pendiente);
    }

    public function test_los_pagos_anulados_no_cuentan(): void
    {
        $idUs = $this->crearEstudiante();
        [, $idImp] = $this->crearProgramaConVersion();
        $idIns = $this->inscribir($idUs, $idImp, $this->crearPlan($idImp, 1000, [1000]));

        $this->pagar($idIns, $idUs, 1000, ['estado' => 0]);

        $this->assertEquals(1000.0, $this->cuotas->sincronizar($idIns)->pendiente);
    }

    public function test_reconoce_pagos_antiguos_sin_inscripcion_por_la_cuota_del_plan(): void
    {
        $idUs = $this->crearEstudiante();
        [, $idImp] = $this->crearProgramaConVersion();
        $idPlan = $this->crearPlan($idImp, 1000, [400, 600]);
        $idIns = $this->inscribir($idUs, $idImp, $idPlan);
        $cuota1 = DB::table('t_fechapago')->where('id_plan', $idPlan)->orderBy('nro_pago')->value('id_fechapago');

        $this->pagar(null, $idUs, 400, ['id_fechapago' => $cuota1]);

        $this->assertEquals(600.0, $this->cuotas->sincronizar($idIns)->pendiente);
    }

    public function test_plan_al_contado_sin_cuotas_cargadas_genera_un_pago_unico(): void
    {
        $idUs = $this->crearEstudiante();
        [, $idImp] = $this->crearProgramaConVersion();
        $idIns = $this->inscribir($idUs, $idImp, $this->crearPlan($idImp, 2000));

        $res = $this->cuotas->sincronizar($idIns);

        $this->assertSame(1, $res->cuotas_totales);
        $this->assertEquals(2000.0, $res->pendiente);
    }

    public function test_plan_fijo_con_cuotas_en_dias_calcula_el_vencimiento_desde_la_inscripcion(): void
    {
        $idUs = $this->crearEstudiante();
        [, $idImp] = $this->crearProgramaConVersion();
        $idPlan = $this->crearPlan($idImp, 900, [300, 600]);
        // Como el plan real del error: marcado "fijo" pero sus cuotas solo tienen días
        DB::table('t_fechapago')->where('id_plan', $idPlan)->update(['fecha_fin' => null]);
        DB::table('t_fechapago')->where('id_plan', $idPlan)->where('nro_pago', '2')->update(['dias_desde_inscripcion' => 30]);
        $idIns = $this->inscribir($idUs, $idImp, $idPlan);

        $this->cuotas->sincronizar($idIns);

        $fechas = DB::table('t_inscripcion_cuotas')->where('id_ins', $idIns)->orderBy('nro_cuota')->pluck('fecha_vencimiento')->all();
        $this->assertSame([now()->toDateString(), now()->addDays(30)->toDateString()], $fechas);
    }

    public function test_plan_gratuito_no_genera_cronograma(): void
    {
        $idUs = $this->crearEstudiante();
        [, $idImp] = $this->crearProgramaConVersion();
        $idIns = $this->inscribir($idUs, $idImp, $this->crearPlan($idImp, 0));

        $this->assertNull($this->cuotas->sincronizar($idIns));
    }

    public function test_un_pago_por_eloquent_recalcula_solo_las_cuotas(): void
    {
        $idUs = $this->crearEstudiante();
        [, $idImp] = $this->crearProgramaConVersion();
        $idIns = $this->inscribir($idUs, $idImp, $this->crearPlan($idImp, 1000, [1000]));
        $this->cuotas->sincronizar($idIns);

        // Como el botón "Pagar" del detalle: pasa por el repositorio (Eloquent)
        $pago = app(PagoRepositoryInterface::class)->create([
            'id_us_reg' => 1, 'id_us' => $idUs, 'id_ins' => $idIns, 'monto_pagado' => 400,
            'fecha_deposito' => now()->toDateString(), 'metodo_pago' => 'efectivo',
            'estado' => 1, 'estado_verificacion' => 'pendiente', 'pago_extra' => 0, 'fecha_reg' => now(),
        ]);
        $this->assertEquals(600.0, $this->saldo($idIns));

        app(PagoRepositoryInterface::class)->anular($pago->id_pago, 1);
        $this->assertEquals(1000.0, $this->saldo($idIns));
    }

    public function test_el_detalle_de_la_inscripcion_muestra_la_misma_deuda_que_el_cronograma(): void
    {
        $idUs = $this->crearEstudiante();
        [, $idImp] = $this->crearProgramaConVersion();
        $idIns = $this->inscribir($idUs, $idImp, $this->crearPlan($idImp, 1900, [300, 800, 800]));
        $this->pagar($idIns, $idUs, 350);

        $detalle = app(InscripcionRepositoryInterface::class)->findDetalleById($idIns);

        $this->assertEquals($this->saldo($idIns), $detalle->resumen->pendiente);
        $this->assertEquals(1550.0, $detalle->resumen->pendiente);
        $this->assertCount(3, $detalle->cuotas_inscripcion);
    }
}
