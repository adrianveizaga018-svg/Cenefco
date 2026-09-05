<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_pago_online_session', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('id_ins');
            $table->unsignedInteger('id_fechapago')->nullable()->comment('NULL = anticipo libre');
            $table->string('proveedor', 40)->default('pagosya');

            $table->string('checkout_id', 200)->unique()->comment('checkout_id de PagosYa');
            $table->string('reference', 200)->unique()->comment('reference de PagosYa (ext_xxxxx)');
            $table->string('checkout_url', 500)->comment('URL de redireccion al usuario');
            $table->string('order_id', 100)->comment('ID propio enviado a PagosYa (cenefco-{id_ins}-{ts})');

            $table->string('sale_id', 200)->nullable()->comment('sale_id devuelto por el webhook');
            $table->string('transaction_id', 200)->nullable()->comment('transaction_id devuelto por el webhook');

            $table->decimal('monto', 12, 2);
            $table->string('moneda', 10)->default('BOB');
            $table->string('estado', 30)->default('pendiente')
                  ->comment('pendiente|pagado|fallido|expirado|cancelado');
            $table->timestamp('expires_at')->nullable();
            $table->json('webhook_payload')->nullable()->comment('Payload completo del webhook recibido');

            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_ins');
            $table->index('checkout_id');
            $table->index('order_id');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_pago_online_session');
    }
};
