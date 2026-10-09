<?php

namespace Tests\Feature\Pagos;

use App\Application\Inscripciones\Services\TransferenciaProgramaService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Support\DatosAcademicos;
use Tests\TestCase;

/** Cambio de programa: los pagos se trasladan sin duplicar y el excedente queda como devolución. */
class TransferenciaProgramaTest extends TestCase
{
    use RefreshDatabase, DatosAcademicos;

    private TransferenciaProgramaService $transferencias;
    private int $idUs;
    private int $idIns;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transferencias = app(TransferenciaProgramaService::class);

        $this->idUs = $this->crearEstudiante();
        [, $idImp] = $this->crearProgramaConVersion('Origen');
        $this->idIns = $this->inscribir($this->idUs, $idImp, $this->crearPlan($idImp, 2000, [1000, 1000]));
        $this->pagar($this->idIns, $this->idUs, 1000);
    }

    /** @return array{0: int, 1: int} [id_imp, id_plan] del programa destino */
    private function destino(float $costo, array $cuotas): array
    {
        [, $idImp] = $this->crearProgramaConVersion('Destino');

        return [$idImp, $this->crearPlan($idImp, $costo, $cuotas)];
    }

    public function test_a_un_plan_mas_caro_traslada_lo_pagado_y_queda_debiendo_la_diferencia(): void
    {
        [$idImp, $idPlan] = $this->destino(3000, [1500, 1500]);
        $totalAntes = (float) DB::table('t_pago')->where('estado', 1)->sum('monto_pagado');

        $res = $this->transferencias->ejecutar($this->idIns, $idImp, $idPlan, 'Prefiere el otro programa', 1);
        $nueva = (int) $res['id_ins_destino'];

        $this->assertSame(0, (int) DB::table('t_inscripcion')->where('id_ins', $this->idIns)->value('estado'));
        $this->assertEquals($totalAntes, (float) DB::table('t_pago')->where('estado', 1)->sum('monto_pagado'));
        $this->assertSame(0, DB::table('t_pago')->where('id_ins', $this->idIns)->where('estado', 1)->count());
        $this->assertEquals(2000.0, $this->saldo($nueva));
        $this->assertSame(0, DB::table('web_devolucion')->count());
    }

    public function test_a_un_plan_mas_barato_registra_el_excedente_como_devolucion_pendiente(): void
    {
        [$idImp, $idPlan] = $this->destino(600, [600]);

        $preview = $this->transferencias->preview($this->idIns, $idImp, $idPlan);
        $this->assertEquals(400.0, $preview['saldo_a_favor']);

        $this->transferencias->ejecutar($this->idIns, $idImp, $idPlan, 'Cambio a un curso corto', 1);
        $nueva = (int) DB::table('t_inscripcion')->where('id_imp', $idImp)->where('estado', 1)->value('id_ins');

        $this->assertEquals(0.0, $this->saldo($nueva));
        $this->assertEquals(400.0, (float) DB::table('web_devolucion')->where('estado', 'pendiente')->value('monto'));
    }

    public function test_no_permite_el_cambio_con_certificado_emitido(): void
    {
        [$idImp, $idPlan] = $this->destino(1000, [1000]);
        $idImpOrigen = DB::table('t_inscripcion')->where('id_ins', $this->idIns)->value('id_imp');
        $plantilla = DB::table('t_cert_plantilla')->insertGetId(['nombre' => 'Aprobación', 'imagen_url' => 'plantillas/a.jpg']);
        $aprobado  = DB::table('t_lista_aprobados')->insertGetId([
            'imparte_id' => $idImpOrigen, 'usuario_id' => $this->idUs, 'inscripcion_id' => $this->idIns, 'estado_certificado' => 'generado',
        ]);
        DB::table('t_certificado')->insert([
            'usuario_id' => $this->idUs, 'imparte_id' => $idImpOrigen, 'plantilla_id' => $plantilla, 'lista_aprobado_id' => $aprobado,
            'codigo_verificacion' => 'prueba-1', 'estado' => 'generado', 'nombre_en_certificado' => 'Ana',
            'programa_en_certificado' => 'Origen', 'condicion' => 'aprobado', 'qr_url' => '', 'archivo_url' => '',
            'id_us_reg' => 1, 'created_at' => now(),
        ]);

        $this->expectException(DomainException::class);
        $this->transferencias->preview($this->idIns, $idImp, $idPlan);
    }
}
