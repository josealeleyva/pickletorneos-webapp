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
            $table->decimal('dupr_rating_min', 4, 2)->nullable()->after('genero_permitido');
            $table->decimal('dupr_rating_max', 4, 2)->nullable()->after('dupr_rating_min');
        });
    }

    public function down(): void
    {
        Schema::table('categoria_torneo', function (Blueprint $table) {
            $table->dropColumn(['dupr_rating_min', 'dupr_rating_max']);
        });
    }
};
