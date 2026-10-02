<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ultimo = \App\Infrastructure\Cursos\Models\Curso::orderBy('id_programa', 'desc')->first();
if ($ultimo) {
    \App\Infrastructure\TareasAcademicas\Models\TareaAcademica::create(['programa_id' => $ultimo->id_programa, 'titulo' => 'Diseñar Temario', 'requiere_archivo' => true, 'estado' => 'pendiente']);
    \App\Infrastructure\TareasAcademicas\Models\TareaAcademica::create(['programa_id' => $ultimo->id_programa, 'titulo' => 'Aprobación de Universidad', 'requiere_archivo' => true, 'estado' => 'pendiente']);
    \App\Infrastructure\TareasAcademicas\Models\TareaAcademica::create(['programa_id' => $ultimo->id_programa, 'titulo' => 'Contratar Docente', 'requiere_archivo' => false, 'estado' => 'pendiente']);
    echo "Tareas creadas para el curso: " . $ultimo->nombre_programa . PHP_EOL;
} else {
    echo "No hay cursos" . PHP_EOL;
}
