<?php

namespace Tests\Unit\Application\Certificados;

use App\Application\Certificados\Services\CertificadoService;
use App\Domain\ListaAprobados\Contracts\ListaAprobadosRepositoryInterface;
use Illuminate\Database\QueryException;
use ReflectionMethod;
use Tests\TestCase;

class CertificadoServiceTest extends TestCase
{
    private function service(): CertificadoService
    {
        $repo = \Mockery::mock(ListaAprobadosRepositoryInterface::class);

        return new CertificadoService($repo);
    }

    private function resolveFont(CertificadoService $service, ?string $campoFuente = null): string
    {
        $method = new ReflectionMethod($service, 'resolveFont');
        $method->setAccessible(true);

        return $method->invoke($service, $campoFuente);
    }

    public function test_resuelve_la_fuente_empaquetada_como_ultimo_recurso(): void
    {
        $rutaEmpaquetada = base_path('assets/fonts/Asap_700.ttf');

        $this->assertFileExists($rutaEmpaquetada);
    }

    public function test_prioriza_campo_fuente_explicito_sobre_los_candidatos(): void
    {
        $rutaEmpaquetada = base_path('assets/fonts/Asap_700.ttf');

        $resultado = $this->resolveFont($this->service(), $rutaEmpaquetada);

        $this->assertSame($rutaEmpaquetada, $resultado);
    }

    public function test_ignora_campo_fuente_si_el_archivo_no_existe(): void
    {
        $resultado = $this->resolveFont($this->service(), '/ruta/inexistente/no-existe.ttf');

        $this->assertNotSame('/ruta/inexistente/no-existe.ttf', $resultado);
        $this->assertFileExists($resultado);
    }

    private function violacionUnicidadPostgres(string $nombreConstraint): QueryException
    {
        $previa = new \PDOException(
            "SQLSTATE[23505]: Unique violation: 7 ERROR: duplicate key value violates unique constraint \"{$nombreConstraint}\""
        );
        $previa->errorInfo = ['23505', 7, "ERROR: duplicate key value violates unique constraint \"{$nombreConstraint}\""];

        return new QueryException('pgsql', 'insert into "t_certificado" ...', [], $previa);
    }

    public function test_detecta_violacion_de_unicidad_de_lista_aprobado(): void
    {
        $service = $this->service();
        $method  = new ReflectionMethod($service, 'esViolacionUnicidadListaAprobado');
        $method->setAccessible(true);

        $excepcion = $this->violacionUnicidadPostgres('t_certificado_lista_aprobado_id_unique');

        $this->assertTrue($method->invoke($service, $excepcion));
    }

    public function test_no_confunde_otra_violacion_de_unicidad_con_la_de_lista_aprobado(): void
    {
        $service = $this->service();
        $method  = new ReflectionMethod($service, 'esViolacionUnicidadListaAprobado');
        $method->setAccessible(true);

        $excepcion = $this->violacionUnicidadPostgres('t_certificado_codigo_verificacion_unique');

        $this->assertFalse($method->invoke($service, $excepcion));
    }

    public function test_no_confunde_una_excepcion_generica_con_violacion_de_unicidad(): void
    {
        $service = $this->service();
        $method  = new ReflectionMethod($service, 'esViolacionUnicidadListaAprobado');
        $method->setAccessible(true);

        $this->assertFalse($method->invoke($service, new \RuntimeException('otro error cualquiera')));
    }
}
