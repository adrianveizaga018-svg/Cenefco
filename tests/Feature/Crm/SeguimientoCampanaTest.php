<?php

namespace Tests\Feature\Crm;

use App\Application\Crm\Services\CrmService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\Support\DatosAcademicos;
use Tests\TestCase;

/** Registro diario de marketing e informe en un clic, unido con Facebook y el CRM. */
class SeguimientoCampanaTest extends TestCase
{
    use RefreshDatabase, DatosAcademicos;

    private int $campana;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->campana = DB::table('campana_publicidad')->insertGetId([
            'nombre' => 'Diplomado Gestión Pública · noviembre', 'plataforma' => 'meta_ads', 'estado' => 'activa',
            'fecha_inicio' => now()->subDays(10)->toDateString(), 'created_at' => now(),
        ]);
        Sanctum::actingAs($this->crearCuenta(null, ['campanas.ver', 'campanas.editar'], 'Marketing'));
    }

    private function registrar(int $diasAtras, int $nuevos, ?int $total = null, float $extra = 0, bool $flyer = false): void
    {
        $this->post("/api/v1/campanas-publicidad/{$this->campana}/registro-diario", array_filter([
            'fecha' => now()->subDays($diasAtras)->toDateString(), 'nuevos_grupo' => $nuevos, 'total_grupo' => $total,
            'gasto_extra' => $extra, 'nota' => "Día -{$diasAtras}",
            'flyer' => $flyer ? UploadedFile::fake()->image('flyer.jpg', 600, 800) : null,
        ], fn ($v) => $v !== null), ['Accept' => 'application/json'])->assertCreated();
    }

    public function test_el_registro_diario_acumula_el_grupo_y_actualiza_leads_wa(): void
    {
        $this->registrar(3, 42, flyer: true);
        $this->registrar(2, 28);
        $this->registrar(1, 30);

        $this->assertSame(100, (int) DB::table('campana_publicidad')->where('id', $this->campana)->value('leads_whatsapp'));
        $this->assertSame(70, (int) DB::table('campana_registro_diario')->where('fecha', now()->subDays(2)->toDateString())->value('total_grupo'));

        // Corregir el mismo día no duplica
        $this->registrar(1, 35);
        $this->assertSame(3, DB::table('campana_registro_diario')->count());
        $this->assertSame(105, (int) DB::table('campana_publicidad')->where('id', $this->campana)->value('leads_whatsapp'));
    }

    public function test_el_resumen_une_facebook_registro_y_crm(): void
    {
        // Facebook guarda el gasto acumulado por corte
        DB::table('campana_metrica')->insert([
            ['campana_publicidad_id' => $this->campana, 'fecha_corte' => now()->subDays(20)->toDateString(), 'gasto_periodo' => 100, 'alcance' => 500, 'impresiones' => 900, 'clics_enlace' => 30, 'fuente' => 'api_meta', 'created_at' => now()],
            ['campana_publicidad_id' => $this->campana, 'fecha_corte' => now()->toDateString(), 'gasto_periodo' => 400, 'alcance' => 3000, 'impresiones' => 6000, 'clics_enlace' => 200, 'fuente' => 'api_meta', 'created_at' => now()],
        ]);
        $this->registrar(3, 60, extra: 50);
        $this->registrar(1, 40);

        // Del grupo, uno se inscribió y pagó
        $idUs = $this->crearEstudiante('3334445');
        DB::table('t_usuario')->where('id_us', $idUs)->update(['celular' => '70000099']);
        [, $idImp] = $this->crearProgramaConVersion();
        app(CrmService::class)->registrar(['nombre' => 'Del grupo', 'celular' => '70000099', 'campana_publicidad_id' => $this->campana], 'whatsapp_grupo');
        $idIns = $this->inscribir($idUs, $idImp, $this->crearPlan($idImp, 1000, [1000]));
        $this->pagar($idIns, $idUs, 700);
        app(CrmService::class)->sincronizarConInscripcion($idIns);

        $r = $this->getJson("/api/v1/campanas-publicidad/{$this->campana}/seguimiento?desde=".now()->subDays(10)->toDateString().'&hasta='.now()->toDateString())
            ->assertOk()->json('resumen');

        $this->assertEquals(300.0, $r['gastos']['facebook']); // 400 acumulado - 100 antes del periodo
        $this->assertEquals(50.0, $r['gastos']['otros']);
        $this->assertSame(100, $r['grupo']['nuevos']);
        $this->assertSame(1, $r['crm']['inscritos']);
        $this->assertEquals(700.0, $r['crm']['cobrado']);
        $this->assertEquals(3.5, $r['costos']['por_persona_grupo']); // 350 / 100
        $this->assertEquals(350.0, $r['costos']['por_inscrito']);
    }

    public function test_el_informe_se_genera_en_pdf_y_queda_en_el_historial(): void
    {
        $this->registrar(2, 20, flyer: true);

        $res = $this->postJson("/api/v1/campanas-publicidad/{$this->campana}/informes", [
            'desde' => now()->subDays(5)->toDateString(), 'hasta' => now()->toDateString(),
        ])->assertCreated();

        $ruta = DB::table('campana_informes')->value('archivo_url');
        Storage::disk('public')->assertExists($ruta);
        $this->assertStringStartsWith('%PDF', Storage::disk('public')->get($ruta));
        $this->assertSame(20, $res->json('informe.resumen.grupo.nuevos'));
        $this->assertCount(1, $this->getJson("/api/v1/campanas-publicidad/{$this->campana}/seguimiento")->json('informes'));
    }

    public function test_sin_permiso_de_edicion_no_registra(): void
    {
        Sanctum::actingAs($this->crearCuenta(null, ['campanas.ver'], 'Lector'));
        $this->postJson("/api/v1/campanas-publicidad/{$this->campana}/registro-diario", ['fecha' => now()->toDateString(), 'nuevos_grupo' => 5])
            ->assertForbidden();
    }
}
