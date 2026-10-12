<?php

namespace Tests\Feature\Crm;

use App\Application\Crm\Services\CrmService;
use App\Infrastructure\CampanasLeads\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\Support\DatosAcademicos;
use Tests\TestCase;

/** Carga masiva desde Excel, secuencia de seguimiento, acciones masivas y tareas del día. */
class SeguimientoMasivoTest extends TestCase
{
    use RefreshDatabase, DatosAcademicos;

    private CrmService $crm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crm = app(CrmService::class);
    }

    private function vendedor(string $nombre): int
    {
        return DB::table('vendedores')->insertGetId(['nombre' => $nombre, 'apellido' => 'Ventas', 'activo' => true, 'created_at' => now()]);
    }

    private function csv(string $contenido): UploadedFile
    {
        return UploadedFile::fake()->createWithContent('contactos.csv', $contenido);
    }

    public function test_importa_el_excel_de_un_grupo_reparte_y_no_duplica(): void
    {
        $this->vendedor('Carla');
        $this->vendedor('Diego');
        $this->crm->registrar(['nombre' => 'Ya estaba', 'celular' => '70000001'], 'whatsapp');
        $idUs = $this->crearEstudiante('5550001');
        DB::table('t_usuario')->where('id_us', $idUs)->update(['celular' => '70000004']);

        Sanctum::actingAs($this->crearCuenta(null, ['crm.importar'], 'Marketing'));
        $res = $this->post('/api/v1/crm/importar', [
            'archivo' => $this->csv("Nombre,Celular\nAna,+591 70000001\nLuis,70000002\nSofía,70000003\nPablo,70000004\nMal,123\n"),
            'origen'  => 'whatsapp_grupo',
            'nota'    => 'Grupo Gestión Pública noviembre',
        ], ['Accept' => 'application/json'])->assertOk();

        $res->assertJsonPath('nuevos', 3)->assertJsonPath('ya_estaban', 1)->assertJsonPath('ya_estudiantes', 1);
        $this->assertCount(1, $res->json('errores'));
        $this->assertSame(4, Lead::count());
        $this->assertSame(3, Lead::where('origen', 'whatsapp_grupo')->count());
        // Reparto en tandas: los nuevos no van todos al mismo vendedor
        $this->assertCount(2, collect($res->json('por_vendedor')));
    }

    public function test_acepta_una_lista_de_numeros_sin_encabezado(): void
    {
        Sanctum::actingAs($this->crearCuenta(null, ['crm.importar'], 'Marketing'));
        $this->post('/api/v1/crm/importar', ['archivo' => $this->csv("71111111\n72222222\n"), 'origen' => 'whatsapp_grupo'], ['Accept' => 'application/json'])
            ->assertOk()->assertJsonPath('nuevos', 2);
    }

    public function test_sin_permiso_de_marketing_no_importa(): void
    {
        Sanctum::actingAs($this->crearCuenta(null, ['crm.ver'], 'Vendedor'));
        $this->post('/api/v1/crm/importar', ['archivo' => $this->csv("Celular\n71111111\n"), 'origen' => 'excel'], ['Accept' => 'application/json'])
            ->assertForbidden();
    }

    public function test_la_secuencia_agenda_los_reintentos_y_cierra_al_tercero_sin_respuesta(): void
    {
        $lead = $this->crm->registrar(['nombre' => 'Silencioso', 'celular' => '70000010'], 'manual');
        $this->assertTrue($lead->proxima_accion_at->isToday()); // primer contacto: hoy

        $this->crm->anotar($lead->fresh(), 'llamada', 'no_contesto', null, null);
        $this->assertSame(now()->addDays(2)->toDateString(), $lead->fresh()->proxima_accion_at->toDateString());

        $this->crm->anotar($lead->fresh(), 'whatsapp', 'no_contesto', null, null);
        $this->assertSame(now()->addDays(3)->toDateString(), $lead->fresh()->proxima_accion_at->toDateString());

        $this->crm->anotar($lead->fresh(), 'llamada', 'no_contesto', null, null);
        $this->assertSame('perdido', $lead->fresh()->estado);
        $this->assertStringContainsString('No responde', $lead->fresh()->motivo_perdida);
    }

    public function test_si_alguna_vez_respondio_no_se_cierra_por_no_contestar(): void
    {
        $lead = $this->crm->registrar(['nombre' => 'Ocupado', 'celular' => '70000011'], 'manual');
        $this->crm->anotar($lead, 'llamada', 'interesado', null, null);
        foreach (range(1, 4) as $_) {
            $this->crm->anotar($lead->fresh(), 'llamada', 'no_contesto', null, null);
        }

        $this->assertSame('interesado', $lead->fresh()->estado);
    }

    public function test_registra_el_mismo_contacto_a_varios_solo_si_son_suyos(): void
    {
        $cuenta = $this->crearCuenta(null, ['crm.ver'], 'Vendedor');
        $mio = DB::table('vendedores')->insertGetId(['nombre' => 'Carla', 'apellido' => 'V', 'activo' => true, 'usuario_id' => $cuenta->id, 'created_at' => now()]);
        $otro = $this->vendedor('Diego');
        $a = $this->crm->registrar(['nombre' => 'A', 'celular' => '70000020', 'vendedor_id' => $mio], 'manual');
        $b = $this->crm->registrar(['nombre' => 'B', 'celular' => '70000021', 'vendedor_id' => $mio], 'manual');
        $ajeno = $this->crm->registrar(['nombre' => 'C', 'celular' => '70000022', 'vendedor_id' => $otro], 'manual');

        Sanctum::actingAs($cuenta);
        $this->postJson('/api/v1/crm/leads/contacto-masivo', [
            'ids' => [$a->id, $b->id, $ajeno->id], 'tipo' => 'whatsapp', 'resultado' => 'contactado', 'nota' => 'Envié el flyer',
        ])->assertOk()->assertJsonPath('registrados', 2);

        $this->assertSame('contactado', $a->fresh()->estado);
        $this->assertSame('nuevo', $ajeno->fresh()->estado);
    }

    public function test_tareas_del_dia_muestra_las_vencidas_y_las_de_hoy(): void
    {
        $cuenta = $this->crearCuenta(null, ['crm.ver'], 'Vendedor');
        $mio = DB::table('vendedores')->insertGetId(['nombre' => 'Carla', 'apellido' => 'V', 'activo' => true, 'usuario_id' => $cuenta->id, 'created_at' => now()]);
        $hoy = $this->crm->registrar(['nombre' => 'Hoy', 'celular' => '70000030', 'vendedor_id' => $mio], 'manual');
        $vencida = $this->crm->registrar(['nombre' => 'Vencida', 'celular' => '70000031', 'vendedor_id' => $mio], 'manual');
        $vencida->update(['proxima_accion_at' => now()->subDays(2)]);
        $futura = $this->crm->registrar(['nombre' => 'Futura', 'celular' => '70000032', 'vendedor_id' => $mio], 'manual');
        $futura->update(['proxima_accion_at' => now()->addDays(3)]);

        Sanctum::actingAs($cuenta);
        $res = $this->getJson('/api/v1/crm/tareas')->assertOk();

        $this->assertEqualsCanonicalizing(['Hoy', 'Vencida'], collect($res->json('data'))->pluck('nombre')->all());
        $res->assertJsonPath('vencidas', 1);
    }
}
