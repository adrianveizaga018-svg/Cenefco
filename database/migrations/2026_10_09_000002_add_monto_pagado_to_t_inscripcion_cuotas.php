<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cuánto se lleva pagado de cada cuota. Lo calcula InscripcionCuotasService::sincronizar
 * repartiendo en orden todos los pagos válidos de la inscripción; así una cuota puede
 * quedar "parcial" y el saldo es monto_a_pagar - monto_pagado.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('t_inscripcion_cuotas', 'monto_pagado')) {
            Schema::table('t_inscripcion_cuotas', function (Blueprint $table) {
                $table->decimal('monto_pagado', 12, 2)->default(0);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('t_inscripcion_cuotas', 'monto_pagado')) {
            Schema::table('t_inscripcion_cuotas', function (Blueprint $table) {
                $table->dropColumn('monto_pagado');
            });
        }
    }
};
