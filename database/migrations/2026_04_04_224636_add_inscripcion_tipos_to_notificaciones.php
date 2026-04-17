<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE notificaciones MODIFY COLUMN tipo ENUM(
            'recordatorio',
            'cambio_horario',
            'cambio_cancha',
            'mensaje_masivo',
            'resultado',
            'inscripcion',
            'invitacion_torneo',
            'inscripcion_confirmada',
            'inscripcion_cancelada',
            'nuevo_equipo_inscripto'
        ) NOT NULL DEFAULT 'mensaje_masivo'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE notificaciones MODIFY COLUMN tipo ENUM(
            'recordatorio',
            'cambio_horario',
            'cambio_cancha',
            'mensaje_masivo',
            'resultado',
            'inscripcion'
        ) NOT NULL DEFAULT 'mensaje_masivo'");
    }
};
