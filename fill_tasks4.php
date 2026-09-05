<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$curso = \App\Infrastructure\Cursos\Models\Curso::create([
    'id_programa' => 1,
    'nombre_programa' => 'Diplomado en Gestión Pública',
    'estado' => 1,
    'slug' => 'diplomado-en-gestion-publica'
]);

\App\Infrastructure\TareasAcademicas\Models\TareaAcademica::create(['programa_id' => $curso->id_programa, 'titulo' => 'Diseñar Temario', 'requiere_archivo' => true, 'estado' => 'pendiente']);
\App\Infrastructure\TareasAcademicas\Models\TareaAcademica::create(['programa_id' => $curso->id_programa, 'titulo' => 'Aprobación de Universidad', 'requiere_archivo' => true, 'estado' => 'pendiente']);
\App\Infrastructure\TareasAcademicas\Models\TareaAcademica::create(['programa_id' => $curso->id_programa, 'titulo' => 'Contratar Docente', 'requiere_archivo' => false, 'estado' => 'pendiente']);

echo "Curso y tareas creadas." . PHP_EOL;
