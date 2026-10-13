<?php

namespace Tests\Feature\ControlAcademico;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\Support\DatosAcademicos;
use Tests\TestCase;

/** Los requisitos del catálogo asignados a un curso deben verse al abrirlo para editar. */
class RequisitosDelCursoTest extends TestCase
{
    use RefreshDatabase, DatosAcademicos;

    public function test_el_curso_muestra_los_requisitos_del_catalogo_que_tiene_asignados(): void
    {
        [$idProg] = $this->crearProgramaConVersion();
        $idCat = DB::table('catalogo_tareas_academicas')->insertGetId([
            'titulo' => 'Aprobación de la universidad', 'requiere_archivo' => true, 'estado' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('tareas_academicas')->insert([
            'programa_id' => $idProg, 'titulo' => 'Aprobación de la universidad', 'requiere_archivo' => true,
            'estado' => 'completada', 'catalogo_id' => $idCat, 'created_at' => now(), 'updated_at' => now(),
        ]);

        Sanctum::actingAs($this->crearCuenta(null, ['programas.ver'], 'Operador'));

        // Si llegara vacío, el formulario de edición lo mandaría vacío al guardar y se borraría el requisito
        $this->getJson("/api/v1/cursos/{$idProg}")->assertOk()
            ->assertJsonFragment(['tareas_catalogo_ids' => [$idCat]]);
    }
}
