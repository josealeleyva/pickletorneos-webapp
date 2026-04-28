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
            $table->text('dupr_access_token')->nullable()->after('remember_token');
            $table->timestamp('dupr_token_expires_at')->nullable()->after('dupr_access_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['dupr_access_token', 'dupr_token_expires_at']);
        });
    }
};
