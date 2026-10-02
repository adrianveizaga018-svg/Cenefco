<?php

namespace Tests\Unit\Application\Pagos;

use App\Application\Pagos\Services\PagoCalculadorService;
use Illuminate\Support\Collection;
use Tests\TestCase;

class PagoCalculadorServiceTest extends TestCase
{
    private function pago(string $monto, int $pagoExtra = 0): object
    {
        return (object) ['monto_pagado' => $monto, 'pago_extra' => $pagoExtra];
    }

    public function test_sin_costo_monto_es_indeterminado(): void
    {
        $service = new PagoCalculadorService();

        $resumen = $service->calcularConCostoFijo(0.0, new Collection([$this->pago('100.00')]));

        $this->assertTrue($resumen->plan_no_asignado);
        $this->assertNull($resumen->pendiente);
        $this->assertSame(0.0, $resumen->total_plan);
        $this->assertSame(0, $resumen->cuotas_totales);
        $this->assertSame(0, $resumen->cuotas_pagadas);
        $this->assertSame(100.0, $resumen->total_pagado);
    }

    public function test_pago_completo_da_pendiente_cero(): void
    {
        $service = new PagoCalculadorService();

        $resumen = $service->calcularConCostoFijo(500.0, new Collection([$this->pago('500.00')]));

        $this->assertSame(0.0, $resumen->pendiente);
        $this->assertSame(1, $resumen->cuotas_pagadas);
        $this->assertSame(1, $resumen->cuotas_totales);
    }

    public function test_pago_parcial_da_pendiente_correcto(): void
    {
        $service = new PagoCalculadorService();

        $resumen = $service->calcularConCostoFijo(500.0, new Collection([$this->pago('300.00')]));

        $this->assertSame(200.0, $resumen->pendiente);
        $this->assertSame(0, $resumen->cuotas_pagadas);
    }

    public function test_sobrepago_no_da_pendiente_negativo(): void
    {
        $service = new PagoCalculadorService();

        $resumen = $service->calcularConCostoFijo(500.0, new Collection([$this->pago('700.00')]));

        $this->assertSame(0.0, $resumen->pendiente);
    }

    public function test_total_anticipos_solo_suma_pagos_extra(): void
    {
        $service = new PagoCalculadorService();

        $resumen = $service->calcularConCostoFijo(500.0, new Collection([
            $this->pago('300.00', pagoExtra: 0),
            $this->pago('100.00', pagoExtra: 1),
        ]));

        $this->assertSame(400.0, $resumen->total_pagado);
        $this->assertSame(100.0, $resumen->total_anticipos);
        $this->assertSame(100.0, $resumen->pendiente);
    }
}
