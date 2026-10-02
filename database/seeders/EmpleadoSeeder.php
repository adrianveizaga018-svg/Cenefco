<?php

namespace Database\Seeders;

use App\Infrastructure\Empleados\Models\Empleado;
use Illuminate\Database\Seeder;

class EmpleadoSeeder extends Seeder
{
    public function run(): void
    {
        $empleados = [
            ['nombre_completo' => 'Rosa Ibáñez Quiroga',       'cargo' => 'Directora Administrativa', 'sueldo_mensual' => 6500.00, 'ci' => '4501234', 'correo' => 'rosa.ibanez@cenefco.com',    'celular_personal' => '70011122', 'fecha_ingreso' => '2022-03-01'],
            ['nombre_completo' => 'Carlos Mamani Fernández',   'cargo' => 'Coordinador Académico',    'sueldo_mensual' => 5200.00, 'ci' => '5502345', 'correo' => 'carlos.mamani@cenefco.com',  'celular_personal' => '70022233', 'fecha_ingreso' => '2022-06-15'],
            ['nombre_completo' => 'Daniela Vargas Peña',       'cargo' => 'Encargada de Marketing',   'sueldo_mensual' => 4800.00, 'ci' => '6103456', 'correo' => 'daniela.vargas@cenefco.com', 'celular_personal' => '70033344', 'fecha_ingreso' => '2023-01-10'],
            ['nombre_completo' => 'Jorge Quispe Rojas',        'cargo' => 'Soporte Técnico',          'sueldo_mensual' => 4200.00, 'ci' => '5804567', 'correo' => 'jorge.quispe@cenefco.com',   'celular_personal' => '70044455', 'fecha_ingreso' => '2023-04-20'],
            ['nombre_completo' => 'Mariana Choque Salazar',    'cargo' => 'Secretaria Académica',     'sueldo_mensual' => 3800.00, 'ci' => '6205678', 'correo' => 'mariana.choque@cenefco.com', 'celular_personal' => '70055566', 'fecha_ingreso' => '2023-07-01'],
            ['nombre_completo' => 'Fernando Apaza Colque',     'cargo' => 'Asesor de Ventas',         'sueldo_mensual' => 3500.00, 'ci' => '5306789', 'correo' => 'fernando.apaza@cenefco.com', 'celular_personal' => '70066677', 'fecha_ingreso' => '2023-09-05'],
            ['nombre_completo' => 'Patricia Flores Guzmán',    'cargo' => 'Contadora',                'sueldo_mensual' => 5000.00, 'ci' => '4907890', 'correo' => 'patricia.flores@cenefco.com','celular_personal' => '70077788', 'fecha_ingreso' => '2022-11-12'],
            ['nombre_completo' => 'Ricardo Torrez Ledezma',    'cargo' => 'Community Manager',        'sueldo_mensual' => 3600.00, 'ci' => '6408901', 'correo' => 'ricardo.torrez@cenefco.com', 'celular_personal' => '70088899', 'fecha_ingreso' => '2024-02-01'],
        ];

        foreach ($empleados as $e) {
            Empleado::firstOrCreate(
                ['ci' => $e['ci']],
                [
                    'nombre_completo' => $e['nombre_completo'],
                    'cargo'           => $e['cargo'],
                    'sueldo_mensual'  => $e['sueldo_mensual'],
                    'correo'          => $e['correo'],
                    'celular_personal'=> $e['celular_personal'],
                    'fecha_ingreso'   => $e['fecha_ingreso'],
                    'activo'          => true,
                ]
            );
        }
    }
}
