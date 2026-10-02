<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('envio_certificado') && ! Schema::hasTable('envios_certificado')) {
            Schema::rename('envio_certificado', 'envios_certificado');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('envios_certificado') && ! Schema::hasTable('envio_certificado')) {
            Schema::rename('envios_certificado', 'envio_certificado');
        }
    }
};
