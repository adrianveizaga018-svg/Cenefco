<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InscripcionPagoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now()->toDateTimeString();

        
        
        
        
        $inscripciones = [
            ['id_ins'=>1001,'id_us'=>9001,'id_imp'=>1,'id_plan'=>101,'fecha_ins'=>'2025-02-01','periodo'=>'2025-I','gestion'=>'2025','observacion_ins'=>'Inscripción regular'],
            ['id_ins'=>1002,'id_us'=>9002,'id_imp'=>1,'id_plan'=>101,'fecha_ins'=>'2025-02-03','periodo'=>'2025-I','gestion'=>'2025','observacion_ins'=>null],
            ['id_ins'=>1003,'id_us'=>9003,'id_imp'=>2,'id_plan'=>102,'fecha_ins'=>'2025-02-05','periodo'=>'2025-I','gestion'=>'2025','observacion_ins'=>'Inscripción con beca'],
            ['id_ins'=>1004,'id_us'=>9004,'id_imp'=>2,'id_plan'=>102,'fecha_ins'=>'2025-02-10','periodo'=>'2025-I','gestion'=>'2025','observacion_ins'=>null],
            ['id_ins'=>1005,'id_us'=>9005,'id_imp'=>3,'id_plan'=>null,'fecha_ins'=>'2026-01-15','periodo'=>'2026-I','gestion'=>'2026','observacion_ins'=>null],
        ];
        foreach ($inscripciones as $i) {
            if (DB::table('t_inscripcion')->where('id_ins', $i['id_ins'])->exists()) continue;
            DB::table('t_inscripcion')->insertOrIgnore(array_merge($i, ['id_us_reg'=>1,'estado'=>1,'fecha_reg'=>$now]));
        }

        $fechasPago = [
            ['id_fechapago'=>201,'id_plan'=>101,'nro_pago'=>'1ra cuota','monto_a_pagar'=>'583','fecha_inicio'=>'2025-02-01','fecha_fin'=>'2025-02-28','obligatorio'=>1,'tipo_tramite'=>'Matrícula'],
            ['id_fechapago'=>202,'id_plan'=>101,'nro_pago'=>'2da cuota','monto_a_pagar'=>'583','fecha_inicio'=>'2025-03-01','fecha_fin'=>'2025-03-31','obligatorio'=>1,'tipo_tramite'=>'Mensualidad'],
            ['id_fechapago'=>203,'id_plan'=>101,'nro_pago'=>'3ra cuota','monto_a_pagar'=>'583','fecha_inicio'=>'2025-04-01','fecha_fin'=>'2025-04-30','obligatorio'=>1,'tipo_tramite'=>'Mensualidad'],
            ['id_fechapago'=>204,'id_plan'=>102,'nro_pago'=>'1ra cuota','monto_a_pagar'=>'708','fecha_inicio'=>'2025-02-01','fecha_fin'=>'2025-02-28','obligatorio'=>1,'tipo_tramite'=>'Matrícula'],
            ['id_fechapago'=>205,'id_plan'=>102,'nro_pago'=>'2da cuota','monto_a_pagar'=>'708','fecha_inicio'=>'2025-03-01','fecha_fin'=>'2025-03-31','obligatorio'=>1,'tipo_tramite'=>'Mensualidad'],
        ];
        foreach ($fechasPago as $f) {
            if (DB::table('t_fechapago')->where('id_fechapago', $f['id_fechapago'])->exists()) continue;
            DB::table('t_fechapago')->insertOrIgnore(array_merge($f, ['id_us_reg'=>1,'num_fechapago'=>$f['id_fechapago']-200,'estado'=>1,'fecha_reg'=>$now]));
        }

        $pagos = [
            ['id_pago'=>3001,'id_us'=>9001,'id_fechapago'=>201,'monto_pagado'=>'583','nro_boleta_bancaria'=>'BOL-001-2025','fecha_deposito'=>'2025-02-10','observacion_pago'=>null],
            ['id_pago'=>3002,'id_us'=>9002,'id_fechapago'=>201,'monto_pagado'=>'583','nro_boleta_bancaria'=>'BOL-002-2025','fecha_deposito'=>'2025-02-15','observacion_pago'=>null],
            ['id_pago'=>3003,'id_us'=>9003,'id_fechapago'=>204,'monto_pagado'=>'708','nro_boleta_bancaria'=>'BOL-003-2025','fecha_deposito'=>'2025-02-12','observacion_pago'=>'Pago con descuento'],
            ['id_pago'=>3004,'id_us'=>9001,'id_fechapago'=>202,'monto_pagado'=>'583','nro_boleta_bancaria'=>'BOL-004-2025','fecha_deposito'=>'2025-03-05','observacion_pago'=>null],
            ['id_pago'=>3005,'id_us'=>9005,'id_fechapago'=>205,'monto_pagado'=>'708','nro_boleta_bancaria'=>'BOL-005-2026','fecha_deposito'=>'2026-02-08','observacion_pago'=>null],
        ];
        foreach ($pagos as $p) {
            if (DB::table('t_pago')->where('id_pago', $p['id_pago'])->exists()) continue;
            DB::table('t_pago')->insertOrIgnore(array_merge($p, ['id_us_reg'=>1,'estado'=>1,'fecha_reg'=>$now]));
        }

        $ingresos = [
            ['id_ing'=>4001,'fecha'=>'2025-02-10'],
            ['id_ing'=>4002,'fecha'=>'2025-02-15'],
            ['id_ing'=>4003,'fecha'=>'2025-03-05'],
            ['id_ing'=>4004,'fecha'=>'2025-03-12'],
            ['id_ing'=>4005,'fecha'=>'2026-02-08'],
        ];
        foreach ($ingresos as $i) {
            if (DB::table('t_ingreso')->where('id_ing', $i['id_ing'])->exists()) continue;
            DB::table('t_ingreso')->insertOrIgnore(array_merge($i, ['id_us_reg'=>1]));
        }

        $this->command->info('✓ ' . count($inscripciones) . ' inscripciones insertadas.');
        $this->command->info('✓ ' . count($fechasPago) . ' fechas de pago insertadas.');
        $this->command->info('✓ ' . count($pagos) . ' pagos académicos insertados.');
        $this->command->info('✓ ' . count($ingresos) . ' ingresos insertados.');
    }
}
