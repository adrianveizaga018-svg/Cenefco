<?php

namespace Tests\Feature\Pagos;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Support\DatosAcademicos;
use Tests\TestCase;

/** El balance del mes compara ingresos y gastos del mismo mes elegido. */
class DashboardGastosTest extends TestCase
{
    use RefreshDatabase, DatosAcademicos;

    public function test_los_ingresos_son_del_mes_elegido_y_no_del_mes_actual(): void
    {
        $idUs = $this->crearEstudiante();
        [, $idImp] = $this->crearProgramaConVersion();
        $idIns = $this->inscribir($idUs, $idImp, null);
        $pasado = now()->subMonths(2)->startOfMonth()->addDays(4);
        $this->pagar($idIns, $idUs, 500, ['fecha_deposito' => $pasado->toDateString()]);
        $this->pagar($idIns, $idUs, 120, ['fecha_deposito' => now()->toDateString()]);

        Sanctum::actingAs($this->crearCuenta(null, ['gastos.ver'], 'Operador'));

        $this->getJson("/api/v1/dashboard-gastos?anio={$pasado->year}&mes={$pasado->month}")->assertOk()
            ->assertJsonPath('resumen_mes.total_ingresos', 500);
        $this->getJson('/api/v1/dashboard-gastos')->assertOk()
            ->assertJsonPath('resumen_mes.total_ingresos', 120);
    }
}
