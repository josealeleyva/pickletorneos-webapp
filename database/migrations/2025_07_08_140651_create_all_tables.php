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
        // Laravel migrations base según tu ER actualizado para PickleTorneos
        // Incluye claves foráneas y relaciones preparadas para seeds y factories

        // 1. Deportes
        Schema::create('deportes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Padel, Futbol
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Categorias
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deporte_id')->constrained('deportes')->onDelete('cascade');
            $table->string('nombre'); // 8va, 7ma, 6ta, +30, Libre, etc.
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Agregar campos a tabla users existente
        Schema::table('users', function (Blueprint $table) {
            $table->string('apellido')->nullable()->after('name');
            $table->string('telefono')->nullable()->after('email');
            $table->string('foto')->nullable()->after('telefono');
            $table->foreignId('deporte_principal_id')->nullable()->after('foto')->constrained('deportes')->onDelete('set null');
            $table->string('organizacion')->nullable()->after('deporte_principal_id'); // Nombre de la organización (para organizadores)
            $table->boolean('cuenta_activa')->default(true)->after('organizacion'); // Para habilitar/deshabilitar cuenta
            $table->integer('torneos_creados')->default(0)->after('cuenta_activa'); // Contador de torneos creados
        });

        // 4. Jugadores (para jugadores que NO tienen cuenta en la app)
        Schema::create('jugadores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('dni')->nullable();
            $table->string('foto')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Si luego se registra en la app
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Jugador_Deporte_Categoria
        Schema::create('jugadores_deportes_categorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jugador_id')->constrained('jugadores')->onDelete('cascade');
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
            $table->foreignId('deporte_id')->constrained('deportes')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        // 6. Complejos Deportivos
        Schema::create('complejos_deportivos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('direccion');
            $table->string('latitud')->nullable();
            $table->string('longitud')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('organizador_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        // 7. Canchas
        Schema::create('canchas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('numero');
            $table->foreignId('complejo_id')->constrained('complejos_deportivos')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        // 8. Formatos Torneos
        Schema::create('formatos_torneos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Eliminación Directa, Fase de Grupos + Eliminación, Liga
            $table->boolean('tiene_grupos');
            $table->timestamps();
            $table->softDeletes();
        });

        // 9. Tamaños Grupos
        Schema::create('tamanios_grupos', function (Blueprint $table) {
            $table->id();
            $table->integer('tamanio'); // 3, 4, 5 equipos por grupo
            $table->timestamps();
            $table->softDeletes();
        });

        // 10. Avances de Grupos
        Schema::create('avances_grupos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // "Solo 1ros", "1ros y 2dos", "1ros + X mejores 2dos"
            $table->integer('cantidad_avanza_directo'); // Cuántos avanzan de cada grupo directo
            $table->integer('cantidad_avanza_mejores')->default(0); // Cuántos mejores segundos/terceros
            $table->timestamps();
            $table->softDeletes();
        });

        // 11. Torneos
        Schema::create('torneos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('deporte_id')->constrained('deportes')->onDelete('cascade');
            $table->text('descripcion')->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->date('fecha_limite_inscripcion')->nullable();
            $table->string('imagen_banner')->nullable();
            $table->text('premios')->nullable();
            $table->foreignId('complejo_id')->constrained('complejos_deportivos')->onDelete('cascade');
            $table->foreignId('organizador_id')->constrained('users')->onDelete('cascade');
            $table->decimal('precio_inscripcion', 10, 2)->nullable();
            $table->foreignId('formato_id')->nullable()->constrained('formatos_torneos')->onDelete('cascade');
            $table->foreignId('tamanio_grupo_id')->nullable()->constrained('tamanios_grupos')->onDelete('set null');
            $table->foreignId('avance_grupos_id')->nullable()->constrained('avances_grupos')->onDelete('set null');
            $table->enum('estado', ['borrador', 'activo', 'en_curso', 'finalizado', 'cancelado'])->default('borrador');
            $table->timestamps();
            $table->softDeletes();
        });

        // 12. Inscripciones
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('torneo_id')->constrained('torneos')->onDelete('cascade');
            $table->foreignId('jugador_id')->constrained('jugadores')->onDelete('cascade');
            $table->enum('estado', ['pendiente', 'confirmada', 'pagada', 'rechazada'])->default('pendiente');
            $table->timestamp('fecha_inscripcion')->useCurrent();
            $table->boolean('pagado')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // 13. Grupos
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Grupo A, Grupo B, etc.
            $table->integer('orden')->nullable();
            $table->foreignId('torneo_id')->constrained('torneos')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        // 14. Equipos
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable(); // Nombre del equipo (opcional)
            $table->foreignId('torneo_id')->constrained('torneos')->onDelete('cascade');
            $table->foreignId('grupo_id')->nullable()->constrained('grupos')->onDelete('set null');
            $table->boolean('es_cabeza_serie')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // 15. Equipo_Jugador (relación many-to-many para soportar equipos de fútbol)
        Schema::create('equipo_jugador', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_id')->constrained('equipos')->onDelete('cascade');
            $table->foreignId('jugador_id')->constrained('jugadores')->onDelete('cascade');
            $table->integer('orden')->nullable(); // Para identificar jugador1, jugador2, etc.
            $table->timestamps();
        });

        // 16. Llaves
        Schema::create('llaves', function (Blueprint $table) {
            $table->id();
            $table->integer('orden');
            $table->string('ronda'); // Final, Semifinal, Cuartos, 8vos, 16vos, etc.
            $table->foreignId('equipo1_id')->nullable()->constrained('equipos')->onDelete('set null');
            $table->foreignId('equipo2_id')->nullable()->constrained('equipos')->onDelete('set null');
            $table->foreignId('torneo_id')->constrained('torneos')->onDelete('cascade');
            $table->foreignId('proxima_llave_id')->nullable()->constrained('llaves')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // 17. Partidos
        Schema::create('partidos', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha_hora')->nullable();
            $table->foreignId('equipo1_id')->constrained('equipos')->onDelete('cascade');
            $table->foreignId('equipo2_id')->constrained('equipos')->onDelete('cascade');
            $table->foreignId('cancha_id')->nullable()->constrained('canchas')->onDelete('set null');
            $table->foreignId('grupo_id')->nullable()->constrained('grupos')->onDelete('set null');
            $table->foreignId('llave_id')->nullable()->constrained('llaves')->onDelete('set null');
            $table->foreignId('equipo_ganador_id')->nullable()->constrained('equipos')->onDelete('set null');
            $table->integer('sets_equipo1')->nullable();
            $table->integer('sets_equipo2')->nullable();
            $table->enum('estado', ['programado', 'en_curso', 'finalizado', 'suspendido', 'cancelado'])->default('programado');
            $table->text('observaciones')->nullable(); // "Al término del partido X vs Y", etc.
            $table->timestamps();
            $table->softDeletes();
        });

        // 18. Juegos (sets o parciales por partido - genérico para fútbol y pádel)
        Schema::create('juegos', function (Blueprint $table) {
            $table->id();
            $table->integer('juego_equipo1'); // Ej: 6 (pádel) o 2 (fútbol)
            $table->integer('juego_equipo2'); // Ej: 4 (pádel) o 1 (fútbol)
            $table->foreignId('partido_id')->constrained('partidos')->onDelete('cascade');
            $table->integer('orden')->default(1); // Para identificar set 1, set 2, etc.
            $table->timestamps();
            $table->softDeletes();
        });

        // 19. Notificaciones
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('torneo_id')->constrained('torneos')->onDelete('cascade');
            $table->text('mensaje');
            $table->enum('tipo', ['recordatorio', 'cambio_horario', 'cambio_cancha', 'mensaje_masivo', 'resultado', 'inscripcion'])->default('mensaje_masivo');
            $table->timestamp('enviado_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 20. Notificaciones_Usuarios (tracking de lectura)
        Schema::create('notificaciones_usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notificacion_id')->constrained('notificaciones')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('leida')->default(false);
            $table->timestamp('leida_at')->nullable();
            $table->timestamps();
        });

        // 21. Actividades_Torneo (log de eventos)
        Schema::create('actividades_torneo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('torneo_id')->constrained('torneos')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Usuario que realizó la acción
            $table->string('tipo'); // cambio_horario, resultado_cargado, mensaje_enviado, equipo_inscrito, etc.
            $table->text('descripcion');
            $table->json('datos')->nullable(); // Datos adicionales en JSON
            $table->timestamps();
        });

        // 22. Pagos_Torneos (sistema de pago por torneo)
        Schema::create('pagos_torneos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('torneo_id')->constrained('torneos')->onDelete('cascade');
            $table->foreignId('organizador_id')->constrained('users')->onDelete('cascade');
            $table->decimal('monto', 10, 2); // Precio del torneo
            $table->enum('estado', ['pendiente', 'pagado', 'vencido', 'cancelado', 'gratuito'])->default('pendiente');
            $table->string('referencia_pago')->nullable(); // ID de transacción de MercadoPago
            $table->string('metodo_pago')->nullable(); // mercadopago, transferencia, etc.
            $table->timestamp('pagado_en')->nullable();
            $table->boolean('es_primer_torneo_gratis')->default(false); // Marca si es el torneo gratuito
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar en orden inverso respetando las foreign keys
        Schema::dropIfExists('pagos_torneos');
        Schema::dropIfExists('actividades_torneo');
        Schema::dropIfExists('notificaciones_usuarios');
        Schema::dropIfExists('notificaciones');
        Schema::dropIfExists('juegos');
        Schema::dropIfExists('partidos');
        Schema::dropIfExists('llaves');
        Schema::dropIfExists('equipo_jugador');
        Schema::dropIfExists('equipos');
        Schema::dropIfExists('grupos');
        Schema::dropIfExists('inscripciones');
        Schema::dropIfExists('torneos');
        Schema::dropIfExists('avances_grupos');
        Schema::dropIfExists('tamanios_grupos');
        Schema::dropIfExists('formatos_torneos');
        Schema::dropIfExists('canchas');
        Schema::dropIfExists('complejos_deportivos');
        Schema::dropIfExists('jugadores_deportes_categorias');
        Schema::dropIfExists('jugadores');

        // Revertir cambios en tabla users
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['deporte_principal_id']);
            $table->dropColumn(['apellido', 'telefono', 'foto', 'deporte_principal_id', 'organizacion', 'cuenta_activa', 'torneos_creados']);
        });

        Schema::dropIfExists('categorias');
        Schema::dropIfExists('deportes');
    }
};
