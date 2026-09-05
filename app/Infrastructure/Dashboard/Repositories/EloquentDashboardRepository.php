<?php
namespace App\Infrastructure\Dashboard\Repositories;
use App\Domain\Dashboard\Contracts\DashboardRepositoryInterface;
use Illuminate\Support\Facades\DB;
class EloquentDashboardRepository implements DashboardRepositoryInterface {
    public function stats(): array {
        return [
            'total_usuarios'     => DB::table('t_usuario')->where('estado', 1)->count(),
            'total_programas'    => DB::table('t_programa')->where('estado', 1)->count(),
            'total_inscripciones'=> DB::table('t_inscripcion')->where('estado', 1)->count(),
            'total_pagos'        => DB::table('t_pago')->where('estado', 1)->count(),
            'total_notas'        => DB::table('t_nota')->where('estado', 1)->count(),
            'total_docentes'     => DB::table('t_imparte')->where('estado', 1)->distinct('id_us')->count('id_us'),
        ];
    }
}
