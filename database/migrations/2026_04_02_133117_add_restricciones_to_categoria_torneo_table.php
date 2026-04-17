<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categoria_torneo', function (Blueprint $table) {
            $table->unsignedTinyInteger('edad_minima')->nullable()->after('campeon_id');
            $table->unsignedTinyInteger('edad_maxima')->nullable()->after('edad_minima');
            $table->enum('genero_permitido', ['masculino', 'femenino', 'mixto'])->nullable()->after('edad_maxima');
        });
    }

    public function down(): void
    {
        Schema::table('categoria_torneo', function (Blueprint $table) {
            $table->dropColumn(['edad_minima', 'edad_maxima', 'genero_permitido']);
        });
    }
};
