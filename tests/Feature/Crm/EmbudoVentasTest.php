<?php

namespace Tests\Feature\Crm;

use App\Application\Crm\Services\CrmService;
use App\Infrastructure\CampanasLeads\Models\Lead;
use App\Infrastructure\Usuarios\Models\User;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\Support\DatosAcademicos;
use Tests\TestCase;

/** Embudo de ventas: entrada sin duplicados, reparto, cierre automático y control por vendedor. */
class EmbudoVentasTest extends TestCase
{
    use RefreshDatabase, DatosAcademicos;

    private CrmService $crm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crm = app(CrmService::class);
    }

    private function crearVendedor(string $nombre, ?User $cuenta = null, float $meta = 0): int
    {
        return DB::table('vendedores')->insertGetId([
            'nombre' => $nombre, 'apellido' => 'Ventas', 'activo' => true, 'meta_ventas' => $meta,
            'usuario_id' => $cuenta?->id, 'created_at' => now(),
        ]);
    }

    public function test_el_mismo_celular_con_o_sin_codigo_de_pais_no_se_duplica(): void
    {
        $a = $this->crm->registrar(['nombre' => 'María', 'celular' => '+591 712-34567'], 'whatsapp');
        $b = $this->crm->registrar(['nombre' => 'María Quispe', 'celular' => '71234567'], 'contacto');

        $this->assertSame($a->id, $b->id);
        $this->assertSame(1, Lead::count());
        $this->assertSame('whatsapp', $b->origen);
    }

    public function test_reparte_por_turnos_entre_vendedores_activos(): void
    {
        $v1 = $this->crearVendedor('Carla');
        $v2 = $this->crearVendedor('Diego');

        $asignados = collect(['70000001', '70000002', '70000003', '70000004'])
            ->map(fn ($cel) => $this->crm->registrar(['nombre' => 'X', 'celular' => $cel], 'manual')->vendedor_asignado_id);

        $this->assertSame(2, $asignados->filter(fn ($v) => $v === $v1)->count());
        $this->assertSame(2, $asignados->filter(fn ($v) => $v === $v2)->count());
    }

    public function test_un_contacto_avanza_la_etapa_pero_nunca_la_retrocede(): void
    {
        $lead = $this->crm->registrar(['nombre' => 'Pedro', 'celular' => '70000010'], 'manual');

        $this->crm->anotar($lead, 'llamada', 'interesado', 'Quiere el plan de 5 cuotas', now()->addDay()->toDateString());
        $this->assertSame('interesado', $lead->fresh()->estado);

        $this->crm->anotar($lead->fresh(), 'llamada', 'contactado', null, null);
        $this->assertSame('interesado', $lead->fresh()->estado);
        $this->assertNotNull($lead->fresh()->ultimo_contacto_at);
    }

    public function test_perder_exige_motivo_e_inscrito_solo_lo_marca_una_inscripcion(): void
    {
        $lead = $this->crm->registrar(['nombre' => 'Rosa', 'celular' => '70000011'], 'manual');

        try {
            $this->crm->moverEtapa($lead, 'perdido');
            $this->fail('Debió pedir el motivo');
        } catch (DomainException) {
        }
        $this->expectException(DomainException::class);
        $this->crm->moverEtapa($lead, 'inscrito');
    }

    public function test_la_preinscripcion_queda_por_pagar_y_el_primer_pago_la_cierra_como_inscrito(): void
    {
        $idUs = $this->crearEstudiante('4445556');
        DB::table('t_usuario')->where('id_us', $idUs)->update(['celular' => '70000020']);
        [, $idImp] = $this->crearProgramaConVersion();
        $lead = $this->crm->registrar(['nombre' => 'Ana', 'celular' => '+59170000020'], 'whatsapp_grupo');

        $idIns = $this->inscribir($idUs, $idImp, $this->crearPlan($idImp, 1000, [1000]), 'portal');
        $this->crm->sincronizarConInscripcion($idIns);
        $this->assertSame('por_pagar', $lead->fresh()->estado);
        $this->assertSame($idIns, (int) $lead->fresh()->id_ins);

        $this->pagar($idIns, $idUs, 300);
        $this->crm->sincronizarConInscripcion($idIns);
        $this->assertSame('inscrito', $lead->fresh()->estado);
        $this->assertSame(1, Lead::count()); // cerró el mismo interesado, no creó otro
    }

    public function test_caja_inscribe_y_la_venta_aparece_en_el_embudo(): void
    {
        [, $idImp] = $this->crearProgramaConVersion();
        $idPlan = $this->crearPlan($idImp, 500, [500]);
        Sanctum::actingAs($this->crearCuenta(null, ['inscripciones.crear'], 'Cajero'));

        $this->postJson('/api/v1/caja/inscribir', [
            'id_imp' => $idImp, 'id_plan' => $idPlan, 'nombre' => 'luis', 'ci' => '7778889', 'celular' => '70000030',
            'monto_pagado' => 500, 'fecha_deposito' => now()->toDateString(), 'metodo_pago' => 'efectivo',
        ])->assertCreated();

        $lead = Lead::first();
        $this->assertSame('inscrito', $lead->estado);
        $this->assertSame('caja', $lead->origen);
    }

    public function test_solicitar_informacion_del_portal_crea_el_interesado(): void
    {
        // Mismo controlador que el formulario del portal (allí además pide clave de portal y captcha)
        Sanctum::actingAs($this->crearCuenta(null, ['contacto.crear'], 'Secretaria'));
        $this->postJson('/api/v1/mensajes-contacto', [
            'nombre_remitente' => 'Jorge', 'email_remitente' => 'jorge@example.com', 'telefono_remitente' => '70000040',
            'asunto' => 'Diplomado', 'mensaje' => '¿Cuándo empieza?',
        ]);

        $this->assertSame('contacto', Lead::where('celular', '70000040')->value('origen'));
    }

    public function test_el_vendedor_ve_solo_lo_suyo_y_dirección_ve_todo(): void
    {
        $cuentaCarla = $this->crearCuenta(null, ['crm.ver'], 'Vendedor');
        $carla = $this->crearVendedor('Carla', $cuentaCarla);
        $diego = $this->crearVendedor('Diego');
        $this->crm->registrar(['nombre' => 'De Carla', 'celular' => '70000050', 'vendedor_id' => $carla], 'manual');
        $deDiego = $this->crm->registrar(['nombre' => 'De Diego', 'celular' => '70000051', 'vendedor_id' => $diego], 'manual');

        Sanctum::actingAs($cuentaCarla);
        $this->getJson('/api/v1/crm/leads')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.nombre', 'De Carla');
        $this->getJson("/api/v1/crm/leads/{$deDiego->id}")->assertNotFound();
        $this->patchJson("/api/v1/crm/leads/{$deDiego->id}/asignar", ['vendedor_id' => $carla])->assertForbidden();

        Sanctum::actingAs($this->crearCuenta(null, ['crm.ver', 'crm.supervisar'], 'Direccion'));
        $this->getJson('/api/v1/crm/leads')->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_el_tablero_cuenta_inscritos_y_lo_cobrado_por_vendedor(): void
    {
        $carla = $this->crearVendedor('Carla', meta: 1000);
        $idUs = $this->crearEstudiante('1212121');
        DB::table('t_usuario')->where('id_us', $idUs)->update(['celular' => '70000060']);
        [, $idImp] = $this->crearProgramaConVersion();
        $this->crm->registrar(['nombre' => 'Ana', 'celular' => '70000060', 'vendedor_id' => $carla], 'manual');
        $this->crm->registrar(['nombre' => 'Otro', 'celular' => '70000061', 'vendedor_id' => $carla], 'manual');

        $idIns = $this->inscribir($idUs, $idImp, $this->crearPlan($idImp, 1000, [1000]));
        $this->pagar($idIns, $idUs, 800);
        $this->crm->sincronizarConInscripcion($idIns);

        Sanctum::actingAs($this->crearCuenta(null, ['crm.ver', 'crm.supervisar'], 'Direccion'));
        $fila = collect($this->getJson('/api/v1/crm/tablero')->assertOk()->json('vendedores'))->firstWhere('vendedor_id', $carla);

        $this->assertSame(2, $fila['interesados']);
        $this->assertSame(1, $fila['inscritos']);
        $this->assertEquals(50.0, $fila['conversion']);
        $this->assertEquals(800.0, $fila['cobrado']);
        $this->assertEquals(80.0, $fila['avance_meta']);
    }

    public function test_avisa_una_sola_vez_a_quien_tiene_interesados_sin_atender(): void
    {
        $cuenta = $this->crearCuenta(null, ['crm.ver'], 'Vendedor');
        $vendedor = $this->crearVendedor('Carla', $cuenta);
        $lead = $this->crm->registrar(['nombre' => 'Olvidado', 'celular' => '70000070', 'vendedor_id' => $vendedor], 'manual');
        $lead->forceFill(['created_at' => now()->subHours(30)])->save();

        $this->travelTo(now()->next('Monday')->setTime(10, 0));
        $this->artisan('crm:alertas')->assertSuccessful();
        $this->artisan('crm:alertas')->assertSuccessful();

        $this->assertNotNull($lead->fresh()->alerta_enviada_at);
        $this->assertSame(1, DB::table('notificaciones')->where('usuario_id', $cuenta->id)->where('tipo', 'interesado_sin_atender')->count());
    }
}
