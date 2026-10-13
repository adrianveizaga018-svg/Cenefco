<?php

namespace Tests\Feature\Pagos;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\Support\DatosAcademicos;
use Tests\TestCase;

/** Pagos a docentes y publicidad de Facebook entran solos a Gastos, sin duplicarse, y por programa. */
class GastosAutomaticosTest extends TestCase
{
    use RefreshDatabase, DatosAcademicos;

    private function sueldoDocente(int $idProg, int $idImp): int
    {
        $perfil = DB::table('web_docente_perfil')->insertGetId([
            'nombre_completo' => 'Lic. Rosa Mamani', 'slug' => 'rosa-mamani', 'estado' => 'publicado',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return DB::table('web_sueldo_docente')->insertGetId([
            'id_us' => -$perfil, 'id_imp' => $idImp, 'id_programa' => $idProg, 'concepto' => 'Honorario módulo 1',
            'periodo' => now()->format('Y-m'), 'gestion' => now()->year, 'monto_total' => 1500, 'estado' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function gastosDe(string $origen)
    {
        return DB::table('gasto')->where('origen', $origen)->whereNull('deleted_at')->get();
    }

    public function test_pagar_a_un_docente_registra_el_gasto_y_anularlo_lo_quita(): void
    {
        [$idProg, $idImp] = $this->crearProgramaConVersion();
        $idSueldo = $this->sueldoDocente($idProg, $idImp);
        Sanctum::actingAs($this->crearCuenta(null, ['docentes.ver', 'docentes.crear', 'docentes.editar', 'docentes.eliminar', 'gastos.ver', 'gastos.editar', 'gastos.eliminar'], 'Admin'));

        $idPago = $this->postJson("/api/v1/sueldos-docentes/{$idSueldo}/pagos", ['monto_pagado' => 750, 'fecha_pago' => now()->toDateString()])
            ->assertCreated()->json('id');

        $gasto = $this->gastosDe('honorario_docente')->first();
        $this->assertEquals(750, (float) $gasto->monto);
        $this->assertSame($idProg, (int) $gasto->id_programa);
        $this->assertStringContainsString('Rosa Mamani', $gasto->concepto);

        // No se puede borrar desde Gastos: se corrige en su origen
        $this->deleteJson("/api/v1/gastos/{$gasto->id}")->assertStatus(422);

        $this->deleteJson("/api/v1/sueldos-docentes/{$idSueldo}/pagos/{$idPago}")->assertSuccessful();
        $this->assertCount(0, $this->gastosDe('honorario_docente'));
    }

    public function test_la_publicidad_de_facebook_es_un_gasto_por_mes_sin_duplicarse(): void
    {
        [$idProg] = $this->crearProgramaConVersion();
        $idCamp = DB::table('campana_publicidad')->insertGetId([
            'nombre' => 'Diplomado — Facebook', 'plataforma' => 'meta_ads', 'estado' => 'activa', 'programa_id' => $idProg,
            'fecha_inicio' => now()->subMonth()->startOfMonth()->toDateString(), 'created_at' => now(), 'updated_at' => now(),
        ]);
        // Meta guarda el gasto acumulado de la campaña en cada corte
        $mesPasado = now()->subMonthNoOverflow()->startOfMonth();
        foreach ([[$mesPasado->copy()->addDays(9), 200], [$mesPasado->copy()->endOfMonth(), 300], [now()->startOfMonth()->addDay(), 380]] as [$fecha, $acumulado]) {
            DB::table('campana_metrica')->insert([
                'campana_publicidad_id' => $idCamp, 'fecha_corte' => $fecha->toDateString(), 'gasto_periodo' => $acumulado,
                'fuente' => 'api_meta', 'created_at' => now(),
            ]);
        }

        $this->artisan('gastos:automaticos')->assertSuccessful();
        $this->artisan('gastos:automaticos')->assertSuccessful();   // correrlo de nuevo no duplica

        $porMes = $this->gastosDe('publicidad_meta')->keyBy(fn ($g) => substr($g->fecha, 0, 7));
        $this->assertCount(2, $porMes);
        $this->assertEquals(300, (float) $porMes[$mesPasado->format('Y-m')]->monto);
        $this->assertEquals(80, (float) $porMes[now()->format('Y-m')]->monto);
        $this->assertSame($idProg, (int) $porMes[now()->format('Y-m')]->id_programa);
    }

    public function test_el_dashboard_muestra_la_ganancia_de_cada_programa(): void
    {
        [$idProg, $idImp] = $this->crearProgramaConVersion('Diplomado en Auditoría');
        $idIns = $this->inscribir($this->crearEstudiante(), $idImp, null);
        $this->pagar($idIns, 1, 2000);
        $idSueldo = $this->sueldoDocente($idProg, $idImp);

        Sanctum::actingAs($this->crearCuenta(null, ['docentes.editar', 'gastos.ver'], 'Admin'));
        $this->postJson("/api/v1/sueldos-docentes/{$idSueldo}/pagos", ['monto_pagado' => 600, 'fecha_pago' => now()->toDateString()])->assertCreated();

        $fila = collect($this->getJson('/api/v1/dashboard-gastos')->assertOk()->json('rentabilidad.programas'))
            ->firstWhere('id_programa', $idProg);
        $this->assertEquals(2000, $fila['ingresos']);
        $this->assertEquals(600, $fila['docentes']);
        $this->assertEquals(1400, $fila['ganancia']);
        $this->assertEquals(70, $fila['margen']);
    }
}
