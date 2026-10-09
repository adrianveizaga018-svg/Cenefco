<?php

namespace Tests\Support;

use App\Infrastructure\Usuarios\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Datos mínimos de las tablas académicas legadas (t_*) para las pruebas de pagos:
 * estudiante, programa publicado con una versión abierta, planes, inscripciones y pagos.
 */
trait DatosAcademicos
{
    protected function crearEstudiante(string $ci = '1234567', string $nombre = 'Ana'): int
    {
        $idUs = (int) (DB::table('t_usuario')->max('id_us') ?? 9000) + 1;
        DB::table('t_usuario')->insert([
            'id_us' => $idUs, 'id_us_reg' => 1, 'nombre' => $nombre, 'appaterno' => 'Prueba',
            'ci' => $ci, 'email' => strtolower($nombre).$idUs.'@example.com', 'celular' => '7'.$idUs,
            'estado' => 1, 'tipoestudiante' => '2', 'fecha_reg' => now(),
        ]);

        return $idUs;
    }

    /** Cuenta de acceso enlazada al estudiante por CI, con un rol que tiene los permisos dados. */
    protected function crearCuenta(?string $ci, array $permisos, string $rol = 'Estudiante'): User
    {
        $rolId = DB::table('roles')->where('nombre', $rol)->value('id')
            ?? DB::table('roles')->insertGetId(['nombre' => $rol, 'descripcion' => $rol, 'activo' => true]);

        foreach ($permisos as $codigo) {
            $permisoId = DB::table('permisos')->where('codigo', $codigo)->value('id')
                ?? DB::table('permisos')->insertGetId(['codigo' => $codigo, 'descripcion' => $codigo, 'modulo' => 'Pruebas']);
            DB::table('roles_permisos')->updateOrInsert(['rol_id' => $rolId, 'permiso_id' => $permisoId]);
        }

        $user = User::forceCreate([
            'nombre' => 'Cuenta', 'apellido' => $rol, 'email' => uniqid('cuenta').'@example.com',
            'password_hash' => Hash::make('secreto123'), 'tipo' => 'participante', 'ci' => $ci,
            'activo' => true, 'email_verificado' => true,
        ]);
        DB::table('usuarios_roles')->insert(['usuario_id' => $user->id, 'rol_id' => $rolId, 'asignado_at' => now()]);

        return $user;
    }

    /** Formulario de inscripción propio de un programa (datos y documentos que se llenan en el portal). */
    protected function crearFormulario(): int
    {
        return DB::table('web_formulario')->insertGetId(['nombre' => 'Formulario', 'slug' => uniqid('form-'), 'campos' => '[]', 'activo' => true]);
    }

    /** Programa publicado con una versión abierta; devuelve [id_programa, id_imp]. */
    protected function crearProgramaConVersion(string $nombre = 'Diplomado de prueba', ?int $formularioId = null): array
    {
        $idProg = (int) (DB::table('t_programa')->max('id_programa') ?? 0) + 1;
        $idImp  = (int) (DB::table('t_imparte')->max('id_imp') ?? 9000) + 1;

        DB::table('t_programa')->insert([
            'id_programa' => $idProg, 'id_us_reg' => 1, 'nombre_programa' => $nombre, 'slug' => 'prog-'.$idProg,
            'estado_web' => 'publicado', 'estado' => 1, 'id_imp' => $idImp, 'formulario_id' => $formularioId,
            'fecha_reg' => now(),
        ]);
        DB::table('t_imparte')->insert([
            'id_imp' => $idImp, 'id_us_reg' => 1, 'id_mat' => $idProg, 'estado' => 1,
            'imparte_fecha_inicio' => now()->addMonth()->toDateString(),
            'imparte_fecha_fin'    => now()->addMonths(6)->toDateString(),
            'fecha_reg' => now(),
        ]);

        return [$idProg, $idImp];
    }

    /** Plan con sus cuotas (montos en orden) habilitado para la versión. */
    protected function crearPlan(int $idImp, float $costo, array $cuotas = []): int
    {
        $idPlan = (int) (DB::table('t_plan')->max('id_plan') ?? 1000) + 1;
        DB::table('t_plan')->insert([
            'id_plan' => $idPlan, 'id_us_reg' => 0, 'num_plan' => 0, 'titulo' => 'Plan '.$idPlan,
            'costo' => (string) $costo, 'nro_cuotas' => (string) max(1, count($cuotas)),
            'id_moodle' => 0, 'promocionar' => 0, 'estado' => 1, 'per_modificar' => 0,
            'fecha_reg' => now(), 'modo_fechas' => 'fijo',
        ]);

        foreach ($cuotas as $i => $monto) {
            DB::table('t_fechapago')->insert([
                'id_fechapago' => (int) (DB::table('t_fechapago')->max('id_fechapago') ?? 0) + 1,
                'id_us_reg' => 0, 'nro_pago' => (string) ($i + 1), 'id_plan' => $idPlan,
                'tipo_tramite' => 'Cuota '.($i + 1), 'monto_a_pagar' => $monto,
                'fecha_fin' => now()->addMonths($i)->toDateString(), 'estado' => 1, 'fecha_reg' => now(),
            ]);
        }

        DB::table('imparticion_planes')->insert(['id_imp' => $idImp, 'id_plan' => $idPlan, 'created_at' => now(), 'updated_at' => now()]);

        return $idPlan;
    }

    protected function inscribir(int $idUs, int $idImp, ?int $idPlan, string $origen = 'caja_admin'): int
    {
        $idIns = (int) (DB::table('t_inscripcion')->max('id_ins') ?? 0) + 1;
        DB::table('t_inscripcion')->insert([
            'id_ins' => $idIns, 'id_us_reg' => 1, 'id_us' => $idUs, 'id_imp' => $idImp, 'id_plan' => $idPlan,
            'fecha_ins' => now()->toDateString(), 'fecha_reg' => now(), 'estado' => 1,
            'canal_venta' => 'presencial', 'origen' => $origen,
        ]);

        return $idIns;
    }

    protected function pagar(?int $idIns, int $idUs, float $monto, array $extra = []): int
    {
        $idPago = (int) (DB::table('t_pago')->max('id_pago') ?? 0) + 1;
        DB::table('t_pago')->insert($extra + [
            'id_pago' => $idPago, 'id_us_reg' => 1, 'id_us' => $idUs, 'id_ins' => $idIns,
            'monto_pagado' => $monto, 'fecha_deposito' => now()->toDateString(), 'metodo_pago' => 'efectivo',
            'estado' => 1, 'estado_verificacion' => 'verificado', 'pago_extra' => 0, 'fecha_reg' => now(),
        ]);

        return $idPago;
    }

    /** Lo que falta pagar según el cronograma de la inscripción. */
    protected function saldo(int $idIns): float
    {
        return round((float) DB::table('t_inscripcion_cuotas')->where('id_ins', $idIns)
            ->sum(DB::raw('monto_a_pagar - monto_pagado')), 2);
    }
}
