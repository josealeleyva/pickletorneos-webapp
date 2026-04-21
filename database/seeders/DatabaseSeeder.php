<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\Roles;
use App\Models\ComplejoDeportivo;
use App\Models\Deporte;
use App\Models\Jugador;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Solo Pickleball
        $pickleball = Deporte::firstOrCreate(['nombre' => 'Pickleball']);

        // ===================================
        // SUPERADMINISTRADOR
        // ===================================
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@pickletorneos.com.ar'],
            [
                'name' => 'Super',
                'apellido' => 'Admin',
                'password' => Hash::make('PuNt0d3Or0##2025'),
                'telefono' => '3416123456',
                'cuenta_activa' => true,
                'torneos_creados' => 0,
            ]
        );

        // ===================================
        // ORGANIZADORES
        // ===================================
        $organizador1 = User::firstOrCreate(
            ['email' => 'organizador1@pickletorneos.com'],
            [
                'name' => 'Juan',
                'apellido' => 'Pérez',
                'password' => Hash::make('1234'),
                'telefono' => '3416234567',
                'deporte_principal_id' => $pickleball->id,
                'organizacion' => 'Pickle Center Rosario',
                'cuenta_activa' => true,
                'torneos_creados' => 0,
            ]
        );

        // Generar código de referido para organizador1
        if (! $organizador1->codigo_referido) {
            $organizador1->generarCodigoReferido();
        }

        $complejo1 = ComplejoDeportivo::firstOrCreate(
            ['nombre' => 'Pickle Center Rosario'],
            [
                'organizador_id' => $organizador1->id,
                'direccion' => 'Calle Falsa 123, Rosario, Santa Fe',
                'telefono' => '3416234567',
                'email' => 'info@picklecenterrosario.com',
            ]
        );

        $cancha1 = $complejo1->canchas()->firstOrCreate(
            ['nombre' => 'Cancha 1'],
            [
                'nombre' => 'A',
                'numero' => '1',
            ]
        );

        $cancha2 = $complejo1->canchas()->firstOrCreate(
            ['nombre' => 'Cancha 2'],
            [
                'nombre' => 'B',
                'numero' => '2',
            ]
        );

        $organizador2 = User::firstOrCreate(
            ['email' => 'organizador2@pickletorneos.com'],
            [
                'name' => 'María',
                'apellido' => 'González',
                'password' => Hash::make('1234'),
                'telefono' => '3416345678',
                'deporte_principal_id' => $pickleball->id,
                'organizacion' => 'Complejo La Cancha',
                'cuenta_activa' => true,
                'torneos_creados' => 0,
            ]
        );

        // ===================================
        // JUGADORES CON CUENTA EN LA APP
        // ===================================
        $jugador1User = User::firstOrCreate(
            ['email' => 'jugador1@pickletorneos.com'],
            [
                'name' => 'Carlos',
                'apellido' => 'Rodríguez',
                'password' => Hash::make('1234'),
                'telefono' => '3416456789',
                'deporte_principal_id' => $pickleball->id,
                'cuenta_activa' => true,
                'torneos_creados' => 0,
            ]
        );

        $jugador2User = User::firstOrCreate(
            ['email' => 'jugador2@pickletorneos.com'],
            [
                'name' => 'Ana',
                'apellido' => 'Martínez',
                'password' => Hash::make('1234'),
                'telefono' => '3416567890',
                'deporte_principal_id' => $pickleball->id,
                'cuenta_activa' => true,
                'torneos_creados' => 0,
            ]
        );

        $jugador3User = User::firstOrCreate(
            ['email' => 'jugador3@pickletorneos.com'],
            [
                'name' => 'Diego',
                'apellido' => 'Fernández',
                'password' => Hash::make('1234'),
                'telefono' => '3416678901',
                'deporte_principal_id' => $pickleball->id,
                'cuenta_activa' => true,
                'torneos_creados' => 0,
            ]
        );

        // Crear registros de jugadores vinculados a usuarios
        Jugador::firstOrCreate(
            ['user_id' => $jugador1User->id],
            [
                'nombre' => $jugador1User->name,
                'apellido' => $jugador1User->apellido,
                'email' => $jugador1User->email,
                'telefono' => $jugador1User->telefono,
            ]
        );

        Jugador::firstOrCreate(
            ['user_id' => $jugador2User->id],
            [
                'nombre' => $jugador2User->name,
                'apellido' => $jugador2User->apellido,
                'email' => $jugador2User->email,
                'telefono' => $jugador2User->telefono,
            ]
        );

        Jugador::firstOrCreate(
            ['user_id' => $jugador3User->id],
            [
                'nombre' => $jugador3User->name,
                'apellido' => $jugador3User->apellido,
                'email' => $jugador3User->email,
                'telefono' => $jugador3User->telefono,
            ]
        );

        // ===================================
        // JUGADORES SIN CUENTA (agregados manualmente por organizadores)
        // ===================================
        // Estos jugadores pertenecen al organizador1
        Jugador::firstOrCreate(
            ['email' => 'pedro.lopez@example.com'],
            [
                'nombre' => 'Pedro',
                'apellido' => 'López',
                'dni' => '35123456',
                'telefono' => '3416789012',
                'user_id' => null,
                'organizador_id' => $organizador1->id,
            ]
        );

        Jugador::firstOrCreate(
            ['email' => 'lucia.gomez@example.com'],
            [
                'nombre' => 'Lucía',
                'apellido' => 'Gómez',
                'dni' => '36234567',
                'telefono' => '3416890123',
                'user_id' => null,
                'organizador_id' => $organizador1->id,
            ]
        );

        // Este jugador pertenece al organizador2
        Jugador::firstOrCreate(
            ['dni' => '37345678'],
            [
                'nombre' => 'Roberto',
                'apellido' => 'Sánchez',
                'email' => null,
                'telefono' => '3416901234',
                'user_id' => null,
                'organizador_id' => $organizador2->id,
            ]
        );

        // ===================================
        // LLAMAR A OTROS SEEDERS
        // ===================================
        $this->call([
            CategoriasSeeder::class,
            FormatosTorneosSeeder::class,
            TamaniosGruposSeeder::class,
            AvancesGruposSeeder::class,
            RolSeeder::class,
            PermisosSeeder::class,
            ConfiguracionPreciosSeeder::class,
        ]);

        // ===================================
        // ASIGNAR ROLES A USUARIOS
        // (debe hacerse antes de TorneoConEquiposSeeder que busca por rol)
        // ===================================
        if (! $superadmin->hasRole(Roles::Superadmin->value)) {
            $superadmin->assignRole(Roles::Superadmin->value);
        }

        if (! $organizador1->hasRole(Roles::Organizador->value)) {
            $organizador1->assignRole(Roles::Organizador->value);
        }

        if (! $organizador2->hasRole(Roles::Organizador->value)) {
            $organizador2->assignRole(Roles::Organizador->value);
        }

        if (! $jugador1User->hasRole(Roles::Jugador->value)) {
            $jugador1User->assignRole(Roles::Jugador->value);
        }

        if (! $jugador2User->hasRole(Roles::Jugador->value)) {
            $jugador2User->assignRole(Roles::Jugador->value);
        }

        if (! $jugador3User->hasRole(Roles::Jugador->value)) {
            $jugador3User->assignRole(Roles::Jugador->value);
        }

        // Torneos de prueba (requiere roles ya asignados)
        $this->call([TorneoConEquiposSeeder::class]);

        $this->command->info('✅ Usuarios de ejemplo creados exitosamente!');
        $this->command->info('');
        $this->command->info('📧 Credenciales de acceso:');
        $this->command->info('');
        $this->command->info('SUPERADMINISTRADOR:');
        $this->command->info('  Email: superadmin@pickletorneos.com');
        $this->command->info('  Password: 1234');
        $this->command->info('');
        $this->command->info('ORGANIZADORES:');
        $this->command->info('  Email: organizador1@pickletorneos.com (Pickle Center Rosario)');
        $this->command->info('  Código de Referido: '.$organizador1->codigo_referido);
        $this->command->info('  Email: organizador2@pickletorneos.com (Complejo La Cancha)');
        $this->command->info('  Password: 1234');
        $this->command->info('');
        $this->command->info('JUGADORES (panel de jugador):');
        $this->command->info('  Email: jugador1@pickletorneos.com (Carlos Rodríguez - Pickleball)');
        $this->command->info('  Email: jugador2@pickletorneos.com (Ana Martínez - Pickleball)');
        $this->command->info('  Email: jugador3@pickletorneos.com (Diego Fernández - Pickleball)');
        $this->command->info('  Password: 1234');
        $this->command->info('  jugador1 y jugador2 están en torneos generados por TorneoConEquiposSeeder');
        $this->command->info('');
    }
}
