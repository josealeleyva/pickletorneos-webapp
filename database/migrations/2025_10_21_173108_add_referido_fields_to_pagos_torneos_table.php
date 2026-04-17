<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pagos_torneos', function (Blueprint $table) {
            $table->boolean('descuento_referido_aplicado')->default(false)->after('estado');
            $table->decimal('monto_descuento_referido', 10, 2)->nullable()->after('descuento_referido_aplicado');
            $table->foreignId('credito_referido_id')->nullable()->constrained('creditos_referidos')->after('monto_descuento_referido');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagos_torneos', function (Blueprint $table) {
            $table->dropForeign(['credito_referido_id']);
            $table->dropColumn(['descuento_referido_aplicado', 'monto_descuento_referido', 'credito_referido_id']);
        });
    }
};
