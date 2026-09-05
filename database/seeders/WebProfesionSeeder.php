<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebProfesionSeeder extends Seeder
{
    public function run(): void
    {
        $profesiones = [
            'Ingeniería de Sistemas',
            'Ingeniería Civil',
            'Ingeniería Industrial',
            'Ingeniería Comercial',
            'Ingeniería en Telecomunicaciones',
            'Ingeniería Electrónica',
            'Ingeniería Electromecánica',
            'Ingeniería Petrolera',
            'Ingeniería Ambiental',
            'Arquitectura',
            'Medicina',
            'Enfermería',
            'Odontología',
            'Bioquímica y Farmacia',
            'Nutrición y Dietética',
            'Derecho',
            'Contaduría Pública',
            'Auditoría Financiera',
            'Administración de Empresas',
            'Economía',
            'Psicología',
            'Trabajo Social',
            'Comunicación Social',
            'Ciencias de la Educación',
            'Relaciones Internacionales',
            'Turismo',
            'Agronomía',
            'Veterinaria y Zootecnia',
            'Diseño Gráfico',
            'Otra',
        ];

        foreach ($profesiones as $i => $nombre) {
            DB::table('web_profesion')->updateOrInsert(
                ['nombre' => $nombre],
                ['nombre' => $nombre, 'orden' => $i + 1, 'activo' => true]
            );
        }
    }
}
