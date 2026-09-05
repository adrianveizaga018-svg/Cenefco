<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebFormularioSeeder extends Seeder
{
    public function run(): void
    {
        $ahora = now();

        $cursoId = DB::table('web_formulario')->insertGetId([
            'nombre'      => 'Inscripción a Curso',
            'slug'        => 'inscripcion-curso',
            'descripcion' => 'Formulario estándar de inscripción para cursos de formación continua.',
            'campos'      => json_encode([
                [
                    'nombre_campo' => 'ci',
                    'etiqueta'     => 'Carnet de Identidad',
                    'tipo'         => 'text',
                    'requerido'    => true,
                    'placeholder'  => 'Ej: 12345678',
                    'ayuda'        => 'Ingresa tu número de CI sin extensión.',
                ],
                [
                    'nombre_campo' => 'nombre_completo',
                    'etiqueta'     => 'Nombre completo',
                    'tipo'         => 'text',
                    'requerido'    => true,
                    'placeholder'  => 'Nombre y apellidos',
                ],
                [
                    'nombre_campo' => 'email',
                    'etiqueta'     => 'Correo electrónico',
                    'tipo'         => 'email',
                    'requerido'    => true,
                    'placeholder'  => 'ejemplo@correo.com',
                ],
                [
                    'nombre_campo' => 'celular',
                    'etiqueta'     => 'Celular / WhatsApp',
                    'tipo'         => 'tel',
                    'requerido'    => true,
                    'placeholder'  => '7XXXXXXX',
                ],
            ]),
            'activo'     => true,
            'created_at' => $ahora,
            'updated_at' => $ahora,
        ]);

        $diplomadoId = DB::table('web_formulario')->insertGetId([
            'nombre'      => 'Inscripción a Diplomado',
            'slug'        => 'inscripcion-diplomado',
            'descripcion' => 'Formulario completo de inscripción para diplomados. Incluye datos académicos y documentos.',
            'campos'      => json_encode([
                [
                    'nombre_campo' => 'ci',
                    'etiqueta'     => 'Carnet de Identidad',
                    'tipo'         => 'text',
                    'requerido'    => true,
                    'placeholder'  => 'Ej: 12345678',
                ],
                [
                    'nombre_campo' => 'nombre_completo',
                    'etiqueta'     => 'Nombre completo',
                    'tipo'         => 'text',
                    'requerido'    => true,
                    'placeholder'  => 'Nombre y apellidos',
                ],
                [
                    'nombre_campo' => 'email',
                    'etiqueta'     => 'Correo electrónico',
                    'tipo'         => 'email',
                    'requerido'    => true,
                    'placeholder'  => 'ejemplo@correo.com',
                ],
                [
                    'nombre_campo' => 'celular',
                    'etiqueta'     => 'Celular / WhatsApp',
                    'tipo'         => 'tel',
                    'requerido'    => true,
                    'placeholder'  => '7XXXXXXX',
                ],
                [
                    'nombre_campo' => 'profesion',
                    'etiqueta'     => 'Profesión / Carrera',
                    'tipo'         => 'text',
                    'requerido'    => false,
                    'placeholder'  => 'Ej: Abogado, Ingeniero Civil...',
                ],
                [
                    'nombre_campo' => 'grado_academico',
                    'etiqueta'     => 'Grado académico',
                    'tipo'         => 'select',
                    'requerido'    => false,
                    'opciones'     => ['Licenciatura', 'Técnico Superior', 'Maestría', 'Doctorado', 'Otro'],
                ],
                [
                    'nombre_campo' => 'foto_carnet',
                    'etiqueta'     => 'Fotocopia de CI (PDF o imagen)',
                    'tipo'         => 'file',
                    'requerido'    => false,
                    'ayuda'        => 'Sube una foto o escaneo de tu carnet de identidad.',
                ],
            ]),
            'activo'     => true,
            'created_at' => $ahora,
            'updated_at' => $ahora,
        ]);

        if (DB::getSchemaBuilder()->hasColumn('t_programa', 'formulario_id')) {
            DB::table('t_programa')
                ->whereNull('formulario_id')
                ->where(function ($q) {
                    $q->whereRaw("LOWER(nombre_programa) NOT LIKE '%diplomado%'");
                })
                ->update(['formulario_id' => $cursoId]);

            DB::table('t_programa')
                ->whereNull('formulario_id')
                ->whereRaw("LOWER(nombre_programa) LIKE '%diplomado%'")
                ->update(['formulario_id' => $diplomadoId]);
        }
    }
}
