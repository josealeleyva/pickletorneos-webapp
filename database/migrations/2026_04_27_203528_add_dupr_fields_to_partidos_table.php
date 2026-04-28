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
        Schema::table('partidos', function (Blueprint $table) {
            $table->string('dupr_partido_id')->nullable()->after('equipo_ganador_id');
            $table->boolean('dupr_sincronizado')->default(false)->after('dupr_partido_id');
            $table->timestamp('dupr_sincronizado_at')->nullable()->after('dupr_sincronizado');
            $table->text('dupr_error')->nullable()->after('dupr_sincronizado_at');
        });
    }

    public function down(): void
    {
        Schema::table('partidos', function (Blueprint $table) {
            $table->dropColumn(['dupr_partido_id', 'dupr_sincronizado', 'dupr_sincronizado_at', 'dupr_error']);
        });
    }
};
