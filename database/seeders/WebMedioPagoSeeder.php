<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebMedioPagoSeeder extends Seeder
{
    public function run(): void
    {
        $medios = [
            'Depósito bancario',
            'Transferencia bancaria',
            'QR',
            'Tarjeta de débito/crédito',
            'Efectivo',
            'Pago en línea (Stripe)',
            'Giro / Western Union',
            'Otro',
        ];

        foreach ($medios as $i => $nombre) {
            DB::table('web_medio_pago')->updateOrInsert(
                ['nombre' => $nombre],
                ['nombre' => $nombre, 'orden' => $i + 1, 'activo' => true]
            );
        }
    }
}
