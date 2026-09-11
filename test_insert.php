<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

DB::table('campana_publicidad')->insert([
    'nombre' => '🟠 Andrea - familiar',
    'plataforma' => 'facebook',
    'fecha_inicio' => '2026-08-09',
    'id_campana_externa' => '120251107424320679',
    'estado' => 'activa',
    'presupuesto_usd' => null,
    'presupuesto_bob' => null
]);

echo "Campaña de prueba insertada\n";