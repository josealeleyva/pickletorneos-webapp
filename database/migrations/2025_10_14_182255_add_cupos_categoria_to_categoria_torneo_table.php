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
        Schema::table('categoria_torneo', function (Blueprint $table) {
            $table->integer('cupos_categoria')->nullable()->after('avance_grupos_id');
            $table->unsignedBigInteger('campeon_id')->nullable()->after('cupos_categoria');

            $table->foreign('campeon_id')->references('id')->on('equipos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categoria_torneo', function (Blueprint $table) {
            $table->dropForeign(['campeon_id']);
            $table->dropColumn(['cupos_categoria', 'campeon_id']);
        });
    }
};
