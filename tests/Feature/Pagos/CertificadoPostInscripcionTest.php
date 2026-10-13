<?php

namespace Tests\Feature\Pagos;

use App\Application\CertConfigProgramas\Commands\AprobarSolicitudCommand;
use App\Application\CertConfigProgramas\Handlers\AprobarSolicitudHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\Support\DatosAcademicos;
use Tests\TestCase;

/** Un certificado aprobado desde "Solicitudes" debe llegar al perfil del estudiante. */
class CertificadoPostInscripcionTest extends TestCase
{
    use RefreshDatabase, DatosAcademicos;

    public function test_el_certificado_aprobado_aparece_en_mis_certificados_del_estudiante(): void
    {
        Storage::fake('public');
        Storage::disk('public')->putFileAs('plantillas', UploadedFile::fake()->image('fondo.jpg', 800, 600), 'fondo.jpg');

        $idUs = $this->crearEstudiante('5550001', 'Lucia');
        [$idProg, $idImp] = $this->crearProgramaConVersion();
        DB::table('t_programa')->where('id_programa', $idProg)->update(['id_us_reg' => 0]); // así se guardan los programas reales
        $idIns = $this->inscribir($idUs, $idImp, null);

        $plantilla = DB::table('t_cert_plantilla')->insertGetId(['nombre' => 'Constancia', 'imagen_url' => '/storage/plantillas/fondo.jpg']);
        $config = DB::table('web_cert_config_programa')->insertGetId(['programa_id' => $idProg, 'activo' => true, 'created_at' => now(), 'updated_at' => now()]);
        $item = DB::table('web_cert_config_item')->insertGetId([
            'config_id' => $config, 'plantilla_id' => $plantilla, 'nombre_cert' => 'Certificado de la universidad',
            'precio' => 150, 'es_gratuito' => false, 'orden' => 1, 'activo' => true,
        ]);
        $solicitud = DB::table('web_cert_solicitud')->insertGetId([
            'config_item_id' => $item, 'inscripcion_id' => $idIns, 'usuario_ci' => '5550001', 'usuario_nombre' => 'Lucia Prueba',
            'es_gratuito' => false, 'estado' => 'pendiente_revision', 'created_at' => now(), 'updated_at' => now(),
        ]);

        app(AprobarSolicitudHandler::class)->handle(new AprobarSolicitudCommand($solicitud));

        Sanctum::actingAs($this->crearCuenta('5550001', ['estudiante.certificados']));
        $this->getJson('/api/v1/estudiante/mis-certificados')->assertOk()
            ->assertJsonFragment(['programa_en_certificado' => 'Certificado de la universidad']);
    }
}
