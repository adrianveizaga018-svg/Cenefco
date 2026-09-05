<?php

namespace Tests\Unit\Application\Certificados;

use App\Application\Certificados\Commands\GenerarCertificadosLoteCommand;
use App\Application\Certificados\Handlers\GenerarCertificadosLoteHandler;
use App\Application\Certificados\Services\CertificadoService;
use Tests\TestCase;

class GenerarCertificadosLoteHandlerTest extends TestCase
{
    public function test_delega_los_parametros_del_command_al_servicio(): void
    {
        $service = \Mockery::mock(CertificadoService::class);
        $service->shouldReceive('generarLote')
            ->once()
            ->with(9001, 8, [9013, 9014])
            ->andReturn(['generados' => 2, 'total_aprobados' => 2, 'errores' => [], 'ya_generados' => [], 'certificados' => []]);

        $handler = new GenerarCertificadosLoteHandler($service);

        $resultado = $handler->handle(new GenerarCertificadosLoteCommand(
            imparteId: 9001,
            plantillaId: 8,
            usuarioIds: [9013, 9014],
        ));

        $this->assertSame(2, $resultado['generados']);
    }

    public function test_usuario_ids_es_opcional_y_se_pasa_como_null(): void
    {
        $service = \Mockery::mock(CertificadoService::class);
        $service->shouldReceive('generarLote')
            ->once()
            ->with(9001, 8, null)
            ->andReturn(['generados' => 0, 'total_aprobados' => 0, 'errores' => [], 'ya_generados' => [], 'certificados' => []]);

        $handler = new GenerarCertificadosLoteHandler($service);

        $handler->handle(new GenerarCertificadosLoteCommand(imparteId: 9001, plantillaId: 8));
    }
}
