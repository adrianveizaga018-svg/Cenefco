<?php

namespace Tests\Feature\Chat;

use App\Infrastructure\Usuarios\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\Support\DatosAcademicos;
use Tests\TestCase;

/** Chat interno: privacidad de los directos, grupos, canal de avisos, menciones y adjuntos. */
class ChatInternoTest extends TestCase
{
    use RefreshDatabase, DatosAcademicos;

    private User $ana;
    private User $beto;
    private User $carla;
    private User $jefa;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->ana   = $this->crearCuenta(null, ['chat.usar'], 'Caja');
        $this->beto  = $this->crearCuenta(null, ['chat.usar'], 'Caja');
        $this->carla = $this->crearCuenta(null, ['chat.usar'], 'Ventas');
        $this->jefa  = $this->crearCuenta(null, ['chat.usar', 'chat.administrar'], 'Direccion');
    }

    private function directo(User $de, User $para): int
    {
        Sanctum::actingAs($de);

        return $this->postJson('/api/v1/chat/directo', ['usuario_id' => $para->id])->assertOk()->json('id');
    }

    private function escribir(User $quien, int $conv, array $datos)
    {
        Sanctum::actingAs($quien);

        return $this->post("/api/v1/chat/conversaciones/{$conv}/mensajes", $datos, ['Accept' => 'application/json']);
    }

    public function test_el_directo_es_privado_y_no_se_duplica(): void
    {
        $conv = $this->directo($this->ana, $this->beto);
        $this->assertSame($conv, $this->directo($this->beto, $this->ana));

        $this->escribir($this->ana, $conv, ['cuerpo' => '¿Me pasas el comprobante de Ana Flores?'])->assertCreated();

        Sanctum::actingAs($this->beto);
        $this->getJson("/api/v1/chat/conversaciones/{$conv}/mensajes")->assertOk()->assertJsonPath('mensajes.0.cuerpo', '¿Me pasas el comprobante de Ana Flores?');

        // Ni otra persona ni Dirección leen un directo ajeno
        foreach ([$this->carla, $this->jefa] as $ajeno) {
            Sanctum::actingAs($ajeno);
            $this->getJson("/api/v1/chat/conversaciones/{$conv}/mensajes")->assertNotFound();
            $this->escribir($ajeno, $conv, ['cuerpo' => 'hola'])->assertNotFound();
        }
    }

    public function test_cuenta_no_leidos_y_los_marca_como_leidos(): void
    {
        $conv = $this->directo($this->ana, $this->beto);
        $this->escribir($this->ana, $conv, ['cuerpo' => 'uno'])->assertCreated();
        $ultimo = $this->escribir($this->ana, $conv, ['cuerpo' => 'dos'])->json('id');

        Sanctum::actingAs($this->beto);
        $this->getJson('/api/v1/chat/no-leidos')->assertJsonPath('no_leidos', 2);
        $this->postJson("/api/v1/chat/conversaciones/{$conv}/leido", ['ultimo_id' => $ultimo])->assertOk();
        $this->getJson('/api/v1/chat/no-leidos')->assertJsonPath('no_leidos', 0);
    }

    public function test_solo_quien_administra_crea_grupos_y_solo_los_miembros_los_leen(): void
    {
        Sanctum::actingAs($this->ana);
        $this->postJson('/api/v1/chat/grupos', ['nombre' => 'Caja', 'miembros' => [$this->beto->id]])->assertStatus(422);

        Sanctum::actingAs($this->jefa);
        $grupo = $this->postJson('/api/v1/chat/grupos', ['nombre' => 'Caja y Ventas', 'miembros' => [$this->ana->id, $this->beto->id]])->assertCreated()->json('id');

        $this->escribir($this->ana, $grupo, ['cuerpo' => 'Buen día equipo'])->assertCreated();
        Sanctum::actingAs($this->carla);
        $this->getJson("/api/v1/chat/conversaciones/{$grupo}/mensajes")->assertNotFound();

        Sanctum::actingAs($this->jefa);
        $this->patchJson("/api/v1/chat/grupos/{$grupo}", ['agregar' => [$this->carla->id]])->assertOk();
        Sanctum::actingAs($this->carla);
        $this->getJson("/api/v1/chat/conversaciones/{$grupo}/mensajes")->assertOk()->assertJsonCount(1, 'mensajes');
    }

    public function test_en_avisos_todos_leen_y_solo_direccion_publica(): void
    {
        $avisos = (int) DB::table('chat_conversaciones')->where('tipo', 'anuncios')->value('id');

        $this->escribir($this->ana, $avisos, ['cuerpo' => 'quiero publicar'])->assertStatus(422);
        $this->escribir($this->jefa, $avisos, ['cuerpo' => 'Mañana no hay atención por feriado.'])->assertCreated();

        Sanctum::actingAs($this->beto);
        $this->getJson("/api/v1/chat/conversaciones/{$avisos}/mensajes")->assertOk()->assertJsonPath('mensajes.0.cuerpo', 'Mañana no hay atención por feriado.');
        $this->assertTrue(DB::table('notificaciones')->where('usuario_id', $this->beto->id)->where('tipo', 'chat_aviso')->exists());
    }

    public function test_la_mencion_avisa_en_la_campanita_solo_a_participantes(): void
    {
        Sanctum::actingAs($this->jefa);
        $grupo = $this->postJson('/api/v1/chat/grupos', ['nombre' => 'Ventas', 'miembros' => [$this->ana->id, $this->carla->id]])->json('id');

        $this->escribir($this->ana, $grupo, ['cuerpo' => '@Carla revisa a María', 'menciones' => [$this->carla->id, $this->beto->id]])->assertCreated();

        $this->assertTrue(DB::table('notificaciones')->where('usuario_id', $this->carla->id)->where('tipo', 'chat_mencion')->exists());
        $this->assertFalse(DB::table('notificaciones')->where('usuario_id', $this->beto->id)->where('tipo', 'chat_mencion')->exists());
    }

    public function test_el_adjunto_es_privado_de_la_conversacion(): void
    {
        $conv = $this->directo($this->ana, $this->beto);
        $id = $this->escribir($this->ana, $conv, ['adjunto' => UploadedFile::fake()->create('comprobante.pdf', 200, 'application/pdf')])
            ->assertCreated()->assertJsonPath('adjunto.nombre', 'comprobante.pdf')->json('id');

        Sanctum::actingAs($this->beto);
        $this->get("/api/v1/chat/mensajes/{$id}/adjunto")->assertOk();
        Sanctum::actingAs($this->carla);
        $this->getJson("/api/v1/chat/mensajes/{$id}/adjunto")->assertNotFound();
    }

    public function test_solo_se_borran_los_mensajes_propios(): void
    {
        $conv = $this->directo($this->ana, $this->beto);
        $id = $this->escribir($this->ana, $conv, ['cuerpo' => 'me equivoqué'])->json('id');

        Sanctum::actingAs($this->beto);
        $this->deleteJson("/api/v1/chat/mensajes/{$id}")->assertStatus(422);
        Sanctum::actingAs($this->ana);
        $this->deleteJson("/api/v1/chat/mensajes/{$id}")->assertOk();
        $this->getJson("/api/v1/chat/conversaciones/{$conv}/mensajes")->assertJsonPath('mensajes.0.eliminado', true)->assertJsonPath('mensajes.0.cuerpo', null);
    }

    public function test_un_estudiante_no_entra_al_chat(): void
    {
        Sanctum::actingAs($this->crearCuenta('7777777', ['estudiante.dashboard']));
        $this->getJson('/api/v1/chat/conversaciones')->assertForbidden();
    }

    public function test_la_busqueda_solo_encuentra_mensajes_de_mis_conversaciones(): void
    {
        $mia = $this->directo($this->ana, $this->beto);
        $ajena = $this->directo($this->carla, $this->jefa);
        $this->escribir($this->ana, $mia, ['cuerpo' => 'horario del diplomado de noviembre'])->assertCreated();
        $this->escribir($this->carla, $ajena, ['cuerpo' => 'el diplomado secreto'])->assertCreated();

        Sanctum::actingAs($this->beto);
        $this->getJson('/api/v1/chat/buscar?q=diplomado')->assertOk()->assertJsonCount(1, 'data');
    }
}
