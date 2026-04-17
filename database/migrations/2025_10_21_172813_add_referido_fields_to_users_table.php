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
        Schema::table('users', function (Blueprint $table) {
            $table->string('codigo_referido', 10)->unique()->nullable()->after('email');
            $table->foreignId('referido_por_id')->nullable()->constrained('users')->after('codigo_referido');
            $table->integer('total_referidos_activos')->default(0)->after('referido_por_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referido_por_id']);
            $table->dropColumn(['codigo_referido', 'referido_por_id', 'total_referidos_activos']);
        });
    }
};
