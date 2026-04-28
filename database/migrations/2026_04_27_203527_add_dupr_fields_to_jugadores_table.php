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
        Schema::table('jugadores', function (Blueprint $table) {
            $table->string('dupr_id', 10)->nullable()->unique()->after('id');
            $table->decimal('rating_singles', 4, 2)->nullable()->after('dupr_id');
            $table->decimal('rating_doubles', 4, 2)->nullable()->after('rating_singles');
            $table->timestamp('dupr_sincronizado_at')->nullable()->after('rating_doubles');
        });
    }

    public function down(): void
    {
        Schema::table('jugadores', function (Blueprint $table) {
            $table->dropUnique(['dupr_id']);
            $table->dropColumn(['dupr_id', 'rating_singles', 'rating_doubles', 'dupr_sincronizado_at']);
        });
    }
};
