<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$sinImparticion = DB::table('t_programa as p')
    ->whereNotExists(function($q) {
        $q->select(DB::raw(1))->from('t_imparte')->whereColumn('id_mat','p.id_programa');
    })
    ->where('p.estado_web','publicado')
    ->select('p.id_programa','p.nombre_programa','p.inicio_actividades','p.finalizacion_actividades')
    ->get();

foreach ($sinImparticion as $prog) {
    $idImp = (DB::table('t_imparte')->max('id_imp') ?? 0) + 1;
    DB::table('t_imparte')->insert([
        'id_imp'               => $idImp,
        'id_mat'               => $prog->id_programa,
        'nombre'               => 'Versión 1',
        'periodo'              => null,
        'gestion'              => now()->year,
        'imparte_fecha_inicio' => $prog->inicio_actividades,
        'imparte_fecha_fin'    => $prog->finalizacion_actividades,
        'estado'               => 1,
    ]);
    echo "Creada imparticion para: {$prog->nombre_programa}\n";
}
if ($sinImparticion->isEmpty()) {
    echo "Todos los programas publicados ya tienen imparticion.\n";
}
