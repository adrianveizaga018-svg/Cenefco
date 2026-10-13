<?php

namespace Tests\Feature\Crm;

use App\Application\Comisiones\Services\RankingVentasService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\Support\DatosAcademicos;
use Tests\TestCase;

/**
 * Comisiones por monto fijo según tipo de producto y rango (tabla de CENEFCO), solo con 50 % pagado,
 * ventas negociadas con monto acordado, y podio mensual con felicitaciones.
 */
class ComisionesYPodioTest extends TestCase
{
    use RefreshDatabase, DatosAcademicos;

    private function producto(string $nombre): int
    {
        return (int) DB::table('comision_productos')->where('nombre', $nombre)->value('id');
    }

    private function vendedor(string $nombre): array
    {
        $cuenta = $this->crearCuenta(null, ['crm.ver'], 'Ventas');
        $id = DB::table('vendedores')->insertGetId([
            'nombre' => $nombre, 'apellido' => 'Vendedora', 'activo' => true, 'usuario_id' => $cuenta->id,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return [$id, $cuenta];
    }

    /** Venta de un programa del tipo dado, con el precio dado, hecha por el vendedor. */
    private function venta(string $producto, float $precio, int $vendedorId, string $ci): int
    {
        [$idProg, $idImp] = $this->crearProgramaConVersion("Programa {$ci}");
        DB::table('t_programa')->where('id_programa', $idProg)->update(['comision_producto_id' => $this->producto($producto)]);
        $idPlan = $this->crearPlan($idImp, $precio, [$precio]);
        $idIns = $this->inscribir($this->crearEstudiante($ci, "Est{$ci}"), $idImp, $idPlan);
        DB::table('t_inscripcion')->where('id_ins', $idIns)->update(['id_vendedor' => $vendedorId]);

        return $idIns;
    }

    private function ventas(string $desde, string $hasta)
    {
        return collect($this->getJson("/api/v1/comisiones-ventas?desde={$desde}&hasta={$hasta}")->assertOk()->json('data'))->keyBy('id_ins');
    }

    public function test_la_tabla_paga_por_rango_y_las_reservas_no_comisionan(): void
    {
        [$ana] = $this->vendedor('Ana');
        $hoy = now()->toDateString();

        $curso = $this->venta('Cursos pasados', 120, $ana, '100');   // rango 101–150 → Bs 20
        $aval  = $this->venta('Cursos con aval', 180, $ana, '101');  // rango 171–190 → Bs 30
        $rm    = $this->venta('Cursos con RM', 450, $ana, '102');    // monto único → Bs 80
        $caro  = $this->venta('Cursos pasados', 250, $ana, '103');   // fuera de la tabla

        $this->pagar($curso, 1, 40);  // reserva: menos del 50 %
        $this->pagar($aval, 1, 180);
        $this->pagar($rm, 1, 225);    // justo el 50 %
        $this->pagar($caro, 1, 250);

        Sanctum::actingAs($this->crearCuenta(null, ['comisiones.ver'], 'Contabilidad'));
        $ventas = $this->ventas($hoy, $hoy);
        $this->assertArrayNotHasKey($curso, $ventas->all(), 'Una reserva no comisiona');
        $this->assertEquals(30, $ventas[$aval]['comision_monto']);
        $this->assertEquals(80, $ventas[$rm]['comision_monto']);
        $this->assertNull($ventas[$caro]['comision_monto']);
        $this->assertSame('fuera_de_tabla', $ventas[$caro]['estado_comision']);

        // Al completar el 50 % la venta del curso ya comisiona, por su rango
        $this->pagar($curso, 1, 30);
        $this->assertEquals(20, $this->ventas($hoy, $hoy)[$curso]['comision_monto']);
    }

    public function test_un_diplomado_se_negocia_y_el_monto_acordado_entra_a_la_liquidacion(): void
    {
        [$ana] = $this->vendedor('Ana');
        $diplomado = $this->venta('Diplomados', 3500, $ana, '200');
        $this->pagar($diplomado, 1, 3500);
        $hoy = now()->toDateString();

        Sanctum::actingAs($this->crearCuenta(null, ['comisiones.ver', 'comisiones.crear'], 'Contabilidad'));
        $this->assertSame('por_negociar', $this->ventas($hoy, $hoy)[$diplomado]['estado_comision']);
        // Solo Administración/Contabilidad acuerda montos
        $this->postJson('/api/v1/comisiones-acuerdos', ['id_ins' => $diplomado, 'monto' => 350, 'motivo' => 'Lo acordó el jefe'])->assertForbidden();

        Sanctum::actingAs($this->crearCuenta(null, ['comisiones.ver', 'comisiones.crear', 'comisiones.configurar'], 'Admin'));
        $this->postJson('/api/v1/comisiones-acuerdos', ['id_ins' => $diplomado, 'monto' => 350, 'motivo' => 'Negociado con dirección: 10 % del contado'])->assertOk();
        $this->assertSame('acordada', $this->ventas($hoy, $hoy)[$diplomado]['estado_comision']);

        $this->postJson('/api/v1/comisiones', ['vendedor_id' => $ana, 'fecha_desde' => $hoy, 'fecha_hasta' => $hoy])->assertSuccessful()
            ->assertJsonPath('monto_comision', 350);
        $this->assertSame('Monto acordado', DB::table('comisiones_liquidacion_detalle')->where('id_ins', $diplomado)->value('regla'));

        // Ya liquidada: el acuerdo no se puede cambiar sin anular la liquidación
        $this->postJson('/api/v1/comisiones-acuerdos', ['id_ins' => $diplomado, 'monto' => 400, 'motivo' => 'cambio'])->assertStatus(422);
    }

    public function test_el_podio_del_mes_felicita_a_los_tres_primeros_una_sola_vez(): void
    {
        [$ana, $cuentaAna] = $this->vendedor('Ana');
        [$beto] = $this->vendedor('Beto');
        [$carla] = $this->vendedor('Carla');
        [$dani] = $this->vendedor('Dani');
        foreach ([[$ana, 900, '300'], [$beto, 1500, '301'], [$carla, 400, '302'], [$dani, 150, '303']] as [$v, $monto, $ci]) {
            $this->pagar($this->venta('Cursos con RM', $monto, $v, $ci), 1, $monto);
        }
        $mes = now()->format('Y-m');

        Sanctum::actingAs($cuentaAna);
        $ranking = $this->getJson("/api/v1/ventas-ranking?mes={$mes}")->assertOk();
        $this->assertSame(['Beto Vendedora', 'Ana Vendedora', 'Carla Vendedora'], array_slice(array_column($ranking->json('ranking'), 'nombre'), 0, 3));
        $this->assertTrue(collect($ranking->json('ranking'))->firstWhere('vendedor_id', $ana)['es_yo']);

        $podio = app(RankingVentasService::class)->cerrarMes($mes);
        $this->assertTrue($podio['anunciado']);
        $this->assertSame(3, DB::table('ventas_podio')->where('periodo', $mes)->count());
        $this->assertTrue(DB::table('notificaciones')->where('usuario_id', $cuentaAna->id)->where('tipo', 'podio_ventas')->where('titulo', 'like', '%Puesto 2%')->exists());

        $this->assertFalse(app(RankingVentasService::class)->cerrarMes($mes)['anunciado'], 'No se felicita dos veces el mismo mes');
    }
}
