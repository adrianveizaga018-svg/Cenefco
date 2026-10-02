<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartaSeeder extends Seeder
{
    public function run(): void
    {
        $cartas = [
            [
                'id_carta'    => 1,
                'num_carta'   => 1,
                'id_us'       => 1,
                'id_plan'     => null,
                'nombresenor' => 'Carlos Alberto Mamani Quispe',
                'nombretitulo'=> 'Lic.',
                'textocarta1' => 'Por medio de la presente certificamos que el Lic. Carlos Alberto Mamani Quispe, con C.I. 1234567 exp. LP, cursó y aprobó satisfactoriamente el Diplomado en Gestión Pública y Administración del Estado en la gestión 2025.',
                'textocarta2' => 'El mencionado profesional demostró durante su formación un desempeño académico destacado, cumpliendo con todos los requisitos establecidos en el plan de estudios del programa.',
                'textocarta3' => 'Se extiende la presente para los fines que el interesado estime convenientes.',
            ],
            [
                'id_carta'    => 2,
                'num_carta'   => 2,
                'id_us'       => 2,
                'id_plan'     => null,
                'nombresenor' => 'Ana María Flores Condori',
                'nombretitulo'=> 'Dra.',
                'textocarta1' => 'A solicitud de parte, se certifica que la Dra. Ana María Flores Condori, con C.I. 2345678 exp. CBBA, completó exitosamente el programa de Maestría en Administración de Empresas (MBA) con mención en Gestión Estratégica.',
                'textocarta2' => 'La mencionada profesional obtuvo calificaciones sobresalientes y participó activamente en las actividades académicas complementarias del programa.',
                'textocarta3' => 'Se extiende la presente en la ciudad de La Paz, a los ' . now()->format('d') . ' días del mes de ' . now()->translatedFormat('F') . ' del año ' . now()->year . '.',
            ],
            [
                'id_carta'    => 3,
                'num_carta'   => 3,
                'id_us'       => 3,
                'id_plan'     => null,
                'nombresenor' => 'Luis Eduardo Gutierrez Vargas',
                'nombretitulo'=> 'Ing.',
                'textocarta1' => 'Nos complace informar que el Ing. Luis Eduardo Gutierrez Vargas, con C.I. 3456789 exp. SCZ, se encuentra inscrito en el Diplomado en Derecho Empresarial y Contratos Comerciales, Gestión 2026-I.',
                'textocarta2' => 'El mencionado profesional ha cumplido con los requisitos de admisión y se encuentra al día en sus obligaciones académicas y administrativas.',
                'textocarta3' => 'Se extiende la presente certificación a efectos de hacer valer donde corresponda.',
            ],
            [
                'id_carta'    => 4,
                'num_carta'   => 4,
                'id_us'       => 4,
                'id_plan'     => null,
                'nombresenor' => 'María Elena Choque Apaza',
                'nombretitulo'=> 'Lic.',
                'textocarta1' => 'Por la presente hacemos conocer que la Lic. María Elena Choque Apaza, con C.I. 4567890 exp. OR, ha sido seleccionada para participar en el programa de becas institucionales correspondiente a la gestión académica 2026.',
                'textocarta2' => 'Dicha selección se realizó en base a su desempeño académico previo y su situación socioeconómica, de acuerdo a los criterios establecidos en el reglamento de becas vigente.',
                'textocarta3' => 'La presente beca cubre el 50% del costo total del programa al que se inscribió.',
            ],
        ];

        $now = now()->toDateTimeString();
        foreach ($cartas as $c) {
            $existe = DB::table('t_carta')->where('id_carta', $c['id_carta'])->exists();
            if ($existe) continue;
            DB::table('t_carta')->insertOrIgnore(array_merge($c, [
                'id_us_reg' => 1,
                'estado'    => 1,
                'fecha_reg' => $now,
            ]));
        }

        $this->command->info('✓ ' . count($cartas) . ' cartas insertadas en t_carta.');
    }
}
