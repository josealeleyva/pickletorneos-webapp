<?php

namespace Database\Seeders;

use App\Models\Torneo;
use App\Models\Jugador;
use App\Models\User;
use App\Models\Deporte;
use App\Models\Categoria;
use App\Models\FormatoTorneo;
use App\Models\TamanioGrupo;
use App\Models\AvanceGrupo;
use App\Models\PagoTorneo;
use App\Models\ConfiguracionSistema;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TorneoConEquiposSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // 1. Obtener organizador (primer usuario con rol organizador)
            $organizador = User::role('organizador')->first();

            if (!$organizador) {
                $this->command->error('No hay usuarios organizadores. Ejecuta primero UserSeeder.');
                return;
            }

            // 2. Obtener datos necesarios
            $deporte = Deporte::where('nombre', 'Pickleball')->first();
            $complejo = $organizador->complejos()->first();

            if (!$complejo) {
                $this->command->error('El organizador no tiene complejos. Crea uno primero.');
                return;
            }

            $formato = FormatoTorneo::where('nombre', 'Fase de Grupos + Eliminación')->first();

            // Obtener diferentes tamaños y criterios de avance
            $tamanio3 = TamanioGrupo::where('tamanio', 3)->first();
            $tamanio4 = TamanioGrupo::where('tamanio', 4)->first();
            $tamanio5 = TamanioGrupo::where('tamanio', 5)->first();

            $avance1y2 = AvanceGrupo::where('nombre', 'Solo 1ros y 2dos de cada grupo')->first();
            $avance1ymejor2 = AvanceGrupo::where('nombre', '1ros + los 2 mejores 2dos')->first();
            $avanceSolo1ros = AvanceGrupo::where('nombre', 'Solo 1ros de cada grupo')->first();

            // 3. Crear torneo (sin configuración de grupos a nivel torneo)
            $torneo = Torneo::create([
                'nombre' => 'Torneo de Pickleball Multicategoría - Apertura 2025',
                'deporte_id' => $deporte->id,
                'descripcion' => 'Torneo de apertura de la temporada 2025 con múltiples categorías de Pickleball. ¡Todos los niveles bienvenidos!',
                'fecha_inicio' => now()->subDays(5),
                'fecha_fin' => now()->addDays(10),
                'fecha_limite_inscripcion' => now()->subDays(10),
                'premios' => "1er Puesto: $50,000\n2do Puesto: $30,000\n3er Puesto: $15,000",
                'complejo_id' => $complejo->id,
                'organizador_id' => $organizador->id,
                'precio_inscripcion' => 5000,
                'formato_id' => $formato->id,
                'estado' => 'en_curso',
            ]);

            $this->command->info("✓ Torneo '{$torneo->nombre}' creado");

            // 3.1. Crear pago GRATUITO (primer torneo)
            PagoTorneo::create([
                'torneo_id' => $torneo->id,
                'organizador_id' => $organizador->id,
                'monto' => 0,
                'estado' => 'gratuito',
                'es_primer_torneo_gratis' => true,
                'pagado_en' => now(),
                'notas' => 'Primer torneo gratuito del organizador',
            ]);

            // Incrementar contador de torneos creados
            $organizador->increment('torneos_creados');

            $this->command->info("✓ Pago gratuito registrado (primer torneo)");

            // 3.2. Asignar categorías al torneo con configuración DIFERENTE para cada una
            $categoriaMasculino = Categoria::where('deporte_id', $deporte->id)->where('nombre', 'Masculino')->first();
            $categoriaFemenino = Categoria::where('deporte_id', $deporte->id)->where('nombre', 'Femenino')->first();
            $categoriaMixto = Categoria::where('deporte_id', $deporte->id)->where('nombre', 'Mixto')->first();

            // Configuración diferenciada por categoría:
            // - 7ma: 4 grupos de 3 equipos, avanzan 1ros y 2dos
            // - 6ta: 3 grupos de 4 equipos, avanzan 1ros + mejores 2dos
            // - 5ta: 2 grupos de 5 equipos, avanzan solo 1ros
            $torneo->categorias()->attach([
                $categoriaMasculino->id => [
                    'numero_grupos' => 4,
                    'tamanio_grupo_id' => $tamanio3->id,  // 3 equipos por grupo
                    'avance_grupos_id' => $avance1y2->id,  // 1ros y 2dos de cada grupo
                ],
                $categoriaFemenino->id => [
                    'numero_grupos' => 3,
                    'tamanio_grupo_id' => $tamanio4->id,  // 4 equipos por grupo
                    'avance_grupos_id' => $avance1ymejor2->id,  // 1ros + mejores 2dos
                ],
                $categoriaMixto->id => [
                    'numero_grupos' => 2,
                    'tamanio_grupo_id' => $tamanio5->id,  // 5 equipos por grupo
                    'avance_grupos_id' => $avanceSolo1ros->id,  // Solo 1ros
                ],
            ]);

            $this->command->info("✓ Categorías asignadas:");
            $this->command->info("  - Masculino: 4 grupos × 3 equipos = 12 equipos (avanzan 1ros y 2dos)");
            $this->command->info("  - Femenino: 3 grupos × 4 equipos = 12 equipos (avanzan 1ros + mejores 2dos)");
            $this->command->info("  - Mixto: 2 grupos × 5 equipos = 10 equipos (avanzan solo 1ros)");

            // 4. Crear jugadores de ejemplo
            $jugadores = $this->crearJugadores();
            $this->command->info("✓ {$jugadores->count()} jugadores creados");

            // Reemplazar los primeros dos jugadores con los que tienen cuenta vinculada en la app
            // Esto permite probar el panel de jugador con datos reales del seeder
            $jugadorUser1 = User::where('email', 'jugador1@pickletorneos.com')->first();
            $jugadorUser2 = User::where('email', 'jugador2@pickletorneos.com')->first();
            if ($jugadorUser1?->jugador) {
                $jugadores[0] = $jugadorUser1->jugador;
                $this->command->info("✓ jugador1 (Carlos Rodríguez) vinculado al seeder de equipos");
            }
            if ($jugadorUser2?->jugador) {
                $jugadores[1] = $jugadorUser2->jugador;
                $this->command->info("✓ jugador2 (Ana Martínez) vinculada al seeder de equipos");
            }

            // 5. Crear equipos distribuidos en 3 categorías
            // Total: 12 (7ma) + 12 (6ta) + 10 (5ta) = 34 equipos
            $equiposData = [
                // CATEGORÍA 7MA: 12 equipos (4 grupos × 3)
                ['González / Martínez', [$jugadores[0], $jugadores[1]], 'Masculino'],
                ['Fernández / López', [$jugadores[2], $jugadores[3]], 'Masculino'],
                ['Rodríguez / Pérez', [$jugadores[4], $jugadores[5]], 'Masculino'],
                ['García / Sánchez', [$jugadores[6], $jugadores[7]], 'Masculino'],
                ['Romero / Torres', [$jugadores[8], $jugadores[9]], 'Masculino'],
                ['Díaz / Ramírez', [$jugadores[10], $jugadores[11]], 'Masculino'],
                ['Vega / Castro', [$jugadores[12], $jugadores[13]], 'Masculino'],
                ['Morales / Ruiz', [$jugadores[14], $jugadores[15]], 'Masculino'],
                ['Herrera / Molina', [$jugadores[16], $jugadores[17]], 'Masculino'],
                ['Ortiz / Silva', [$jugadores[18], $jugadores[19]], 'Masculino'],
                ['Méndez / Navarro', [$jugadores[20], $jugadores[21]], 'Masculino'],
                ['Campos / Ramos', [$jugadores[22], $jugadores[23]], 'Masculino'],

                // CATEGORÍA 6TA: 12 equipos (3 grupos × 4)
                ['Vargas / Luna', [$jugadores[24], $jugadores[25]], 'Femenino'],
                ['Flores / Guzmán', [$jugadores[26], $jugadores[27]], 'Femenino'],
                ['Cabrera / Ríos', [$jugadores[28], $jugadores[29]], 'Femenino'],
                ['Domínguez / Aguilar', [$jugadores[30], $jugadores[31]], 'Femenino'],
                ['Blanco / Mendoza', [$jugadores[32], $jugadores[33]], 'Femenino'],
                ['Suárez / Giménez', [$jugadores[34], $jugadores[35]], 'Femenino'],
                ['Medina / Benítez', [$jugadores[36], $jugadores[37]], 'Femenino'],
                ['Acosta / Ponce', [$jugadores[38], $jugadores[39]], 'Femenino'],
                ['Rojas / Carrillo', [$jugadores[40], $jugadores[41]], 'Femenino'],
                ['Cortés / Fuentes', [$jugadores[42], $jugadores[43]], 'Femenino'],
                ['Paredes / Salazar', [$jugadores[44], $jugadores[45]], 'Femenino'],
                ['Bravo / Ibáñez', [$jugadores[46], $jugadores[47]], 'Femenino'],

                // CATEGORÍA 5TA: 10 equipos (2 grupos × 5)
                ['Lara / Maldonado', [$jugadores[48], $jugadores[49]], 'Mixto'],
                ['Núñez / Estrada', [$jugadores[50], $jugadores[51]], 'Mixto'],
                ['Sandoval / Leyva', [$jugadores[52], $jugadores[53]], 'Mixto'],
                ['Cárdenas / Montoya', [$jugadores[54], $jugadores[55]], 'Mixto'],
                ['Guerrero / Mejía', [$jugadores[56], $jugadores[57]], 'Mixto'],
                ['Espinoza / León', [$jugadores[58], $jugadores[59]], 'Mixto'],
                ['Reyes / Pacheco', [$jugadores[60], $jugadores[61]], 'Mixto'],
                ['Cervantes / Duarte', [$jugadores[62], $jugadores[63]], 'Mixto'],
                ['Galván / Ochoa', [$jugadores[64], $jugadores[65]], 'Mixto'],
                ['Villarreal / Sosa', [$jugadores[66], $jugadores[67]], 'Mixto'],
            ];

            foreach ($equiposData as [$nombreEquipo, $jugadoresEquipo, $categoriaNombre]) {
                // Determinar categoría ID
                $categoriaId = match($categoriaNombre) {
                    'Masculino' => $categoriaMasculino->id,
                    'Femenino' => $categoriaFemenino->id,
                    'Mixto' => $categoriaMixto->id,
                };

                $equipo = $torneo->equipos()->create([
                    'nombre' => $nombreEquipo,
                    'categoria_id' => $categoriaId,
                ]);

                // Asociar jugadores
                foreach ($jugadoresEquipo as $orden => $jugador) {
                    $equipo->jugadores()->attach($jugador->id, ['orden' => $orden + 1]);
                }

                $this->command->info("✓ Equipo '{$nombreEquipo}' creado (Categoría {$categoriaNombre})");
            }

            // 6. Crear segundo torneo: Liga (antes Round Robin)
            $this->command->info('');
            $this->command->info('===========================================');
            $this->command->info('Creando segundo torneo: Liga');
            $this->command->info('===========================================');

            $formatoLiga = FormatoTorneo::where('nombre', 'Liga')->first();

            $torneoRR = Torneo::create([
                'nombre' => 'Torneo de Pickleball Liga - Clausura 2025',
                'deporte_id' => $deporte->id,
                'descripcion' => 'Torneo de clausura en formato Liga: todos contra todos.',
                'fecha_inicio' => now()->addDays(5),
                'fecha_fin' => now()->addDays(15),
                'fecha_limite_inscripcion' => now()->subDays(5),
                'premios' => "1er Puesto: $40,000\n2do Puesto: $25,000\n3er Puesto: $10,000",
                'complejo_id' => $complejo->id,
                'organizador_id' => $organizador->id,
                'precio_inscripcion' => 4000,
                'formato_id' => $formatoLiga->id,
                'estado' => 'en_curso',
            ]);

            $this->command->info("✓ Torneo '{$torneoRR->nombre}' creado");

            // 6.1. Crear pago PAGADO (segundo torneo)
            $precioTorneo = ConfiguracionSistema::get('precio_torneo', 25000);
            PagoTorneo::create([
                'torneo_id' => $torneoRR->id,
                'organizador_id' => $organizador->id,
                'monto' => $precioTorneo,
                'estado' => 'pagado',
                'es_primer_torneo_gratis' => false,
                'referencia_pago' => 'SEED_' . uniqid(),
                'metodo_pago' => 'mercadopago',
                'pagado_en' => now()->subDays(5),
                'notas' => 'Pago simulado por seeder',
            ]);

            // Incrementar contador de torneos creados
            $organizador->increment('torneos_creados');

            $this->command->info("✓ Pago registrado como pagado (\${$precioTorneo})");

            // 6.2. Asignar categorías con cupos (sin grupos)
            // - 7ma: 6 equipos
            // - 6ta: 8 equipos
            // - 5ta: 6 equipos
            $torneoRR->categorias()->attach([
                $categoriaMasculino->id => [
                    'cupos_categoria' => 6,
                    'numero_grupos' => null,
                    'tamanio_grupo_id' => null,
                    'avance_grupos_id' => null,
                ],
                $categoriaFemenino->id => [
                    'cupos_categoria' => 8,
                    'numero_grupos' => null,
                    'tamanio_grupo_id' => null,
                    'avance_grupos_id' => null,
                ],
                $categoriaMixto->id => [
                    'cupos_categoria' => 6,
                    'numero_grupos' => null,
                    'tamanio_grupo_id' => null,
                    'avance_grupos_id' => null,
                ],
            ]);

            $this->command->info("✓ Categorías asignadas:");
            $this->command->info("  - Masculino: 6 equipos (15 partidos)");
            $this->command->info("  - Femenino: 8 equipos (28 partidos)");
            $this->command->info("  - Mixto: 6 equipos (15 partidos)");

            // 6.2. Crear equipos para Liga (reutilizando algunos jugadores)
            $equiposRRData = [
                // CATEGORÍA 7MA: 6 equipos
                [[$jugadores[0], $jugadores[1]], 'Masculino'],
                [[$jugadores[2], $jugadores[3]], 'Masculino'],
                [[$jugadores[4], $jugadores[5]], 'Masculino'],
                [[$jugadores[6], $jugadores[7]], 'Masculino'],
                [[$jugadores[8], $jugadores[9]], 'Masculino'],
                [[$jugadores[10], $jugadores[11]], 'Masculino'],

                // CATEGORÍA 6TA: 8 equipos
                [[$jugadores[24], $jugadores[25]], 'Femenino'],
                [[$jugadores[26], $jugadores[27]], 'Femenino'],
                [[$jugadores[28], $jugadores[29]], 'Femenino'],
                [[$jugadores[30], $jugadores[31]], 'Femenino'],
                [[$jugadores[32], $jugadores[33]], 'Femenino'],
                [[$jugadores[34], $jugadores[35]], 'Femenino'],
                [[$jugadores[36], $jugadores[37]], 'Femenino'],
                [[$jugadores[38], $jugadores[39]], 'Femenino'],

                // CATEGORÍA 5TA: 6 equipos
                [[$jugadores[48], $jugadores[49]], 'Mixto'],
                [[$jugadores[50], $jugadores[51]], 'Mixto'],
                [[$jugadores[52], $jugadores[53]], 'Mixto'],
                [[$jugadores[54], $jugadores[55]], 'Mixto'],
                [[$jugadores[56], $jugadores[57]], 'Mixto'],
                [[$jugadores[58], $jugadores[59]], 'Mixto'],
            ];

            foreach ($equiposRRData as [$jugadoresEquipo, $categoriaNombre]) {
                // Generar nombre automáticamente con apellidos
                $nombreEquipo = $this->generarNombreEquipo($jugadoresEquipo);
                $categoriaId = match($categoriaNombre) {
                    'Masculino' => $categoriaMasculino->id,
                    'Femenino' => $categoriaFemenino->id,
                    'Mixto' => $categoriaMixto->id,
                };

                $equipo = $torneoRR->equipos()->create([
                    'nombre' => $nombreEquipo,
                    'categoria_id' => $categoriaId,
                ]);

                foreach ($jugadoresEquipo as $orden => $jugador) {
                    $equipo->jugadores()->attach($jugador->id, ['orden' => $orden + 1]);
                }

                $this->command->info("✓ Equipo '{$nombreEquipo}' creado (Categoría {$categoriaNombre})");
            }

            // 7. Crear tercer torneo: Eliminación Directa
            $this->command->info('');
            $this->command->info('===========================================');
            $this->command->info('Creando tercer torneo: Eliminación Directa');
            $this->command->info('===========================================');

            $formatoED = FormatoTorneo::where('nombre', 'Eliminación Directa')->first();

            $torneoED = Torneo::create([
                'nombre' => 'Copa Relámpago - Eliminación Directa 2025',
                'deporte_id' => $deporte->id,
                'descripcion' => 'Copa express en formato de eliminación directa. ¡Sin margen de error!',
                'fecha_inicio' => now()->subDays(30),
                'fecha_fin' => now()->subDays(25),
                'fecha_limite_inscripcion' => now()->subDays(35),
                'premios' => "1er Puesto: $60,000\n2do Puesto: $35,000\n3er Puesto: $20,000",
                'complejo_id' => $complejo->id,
                'organizador_id' => $organizador->id,
                'precio_inscripcion' => 6000,
                'formato_id' => $formatoED->id,
                'estado' => 'finalizado',
            ]);

            $this->command->info("✓ Torneo '{$torneoED->nombre}' creado");

            // 7.1. Crear pago PAGADO (tercer torneo)
            PagoTorneo::create([
                'torneo_id' => $torneoED->id,
                'organizador_id' => $organizador->id,
                'monto' => $precioTorneo,
                'estado' => 'pagado',
                'es_primer_torneo_gratis' => false,
                'referencia_pago' => 'SEED_' . uniqid(),
                'metodo_pago' => 'mercadopago',
                'pagado_en' => now()->subDays(2),
                'notas' => 'Pago simulado por seeder',
            ]);

            // Incrementar contador de torneos creados
            $organizador->increment('torneos_creados');

            $this->command->info("✓ Pago registrado como pagado (\${$precioTorneo})");

            // 7.2. Asignar categorías con cupos (sin grupos)
            // - 7ma: 8 equipos (3 rondas)
            // - 6ta: 16 equipos (4 rondas)
            // - 5ta: 4 equipos (2 rondas)
            $torneoED->categorias()->attach([
                $categoriaMasculino->id => [
                    'cupos_categoria' => 8,
                    'numero_grupos' => null,
                    'tamanio_grupo_id' => null,
                    'avance_grupos_id' => null,
                ],
                $categoriaFemenino->id => [
                    'cupos_categoria' => 16,
                    'numero_grupos' => null,
                    'tamanio_grupo_id' => null,
                    'avance_grupos_id' => null,
                ],
                $categoriaMixto->id => [
                    'cupos_categoria' => 4,
                    'numero_grupos' => null,
                    'tamanio_grupo_id' => null,
                    'avance_grupos_id' => null,
                ],
            ]);

            $this->command->info("✓ Categorías asignadas:");
            $this->command->info("  - Masculino: 8 equipos (3 rondas)");
            $this->command->info("  - Femenino: 16 equipos (4 rondas)");
            $this->command->info("  - Mixto: 4 equipos (2 rondas)");

            // 7.2. Crear equipos para Eliminación Directa (reutilizando algunos jugadores)
            $equiposEDData = [
                // CATEGORÍA 7MA: 8 equipos
                [[$jugadores[12], $jugadores[13]], 'Masculino'],
                [[$jugadores[14], $jugadores[15]], 'Masculino'],
                [[$jugadores[16], $jugadores[17]], 'Masculino'],
                [[$jugadores[18], $jugadores[19]], 'Masculino'],
                [[$jugadores[20], $jugadores[21]], 'Masculino'],
                [[$jugadores[22], $jugadores[23]], 'Masculino'],
                [[$jugadores[0], $jugadores[1]], 'Masculino'],
                [[$jugadores[2], $jugadores[3]], 'Masculino'],

                // CATEGORÍA 6TA: 16 equipos
                [[$jugadores[40], $jugadores[41]], 'Femenino'],
                [[$jugadores[42], $jugadores[43]], 'Femenino'],
                [[$jugadores[44], $jugadores[45]], 'Femenino'],
                [[$jugadores[46], $jugadores[47]], 'Femenino'],
                [[$jugadores[24], $jugadores[25]], 'Femenino'],
                [[$jugadores[26], $jugadores[27]], 'Femenino'],
                [[$jugadores[28], $jugadores[29]], 'Femenino'],
                [[$jugadores[30], $jugadores[31]], 'Femenino'],
                [[$jugadores[32], $jugadores[33]], 'Femenino'],
                [[$jugadores[34], $jugadores[35]], 'Femenino'],
                [[$jugadores[36], $jugadores[37]], 'Femenino'],
                [[$jugadores[38], $jugadores[39]], 'Femenino'],
                [[$jugadores[40], $jugadores[41]], 'Femenino'],
                [[$jugadores[42], $jugadores[43]], 'Femenino'],
                [[$jugadores[44], $jugadores[45]], 'Femenino'],
                [[$jugadores[46], $jugadores[47]], 'Femenino'],

                // CATEGORÍA 5TA: 4 equipos
                [[$jugadores[60], $jugadores[61]], 'Mixto'],
                [[$jugadores[62], $jugadores[63]], 'Mixto'],
                [[$jugadores[64], $jugadores[65]], 'Mixto'],
                [[$jugadores[66], $jugadores[67]], 'Mixto'],
            ];

            foreach ($equiposEDData as [$jugadoresEquipo, $categoriaNombre]) {
                // Generar nombre automáticamente con apellidos
                $nombreEquipo = $this->generarNombreEquipo($jugadoresEquipo);
                $categoriaId = match($categoriaNombre) {
                    'Masculino' => $categoriaMasculino->id,
                    'Femenino' => $categoriaFemenino->id,
                    'Mixto' => $categoriaMixto->id,
                };

                $equipo = $torneoED->equipos()->create([
                    'nombre' => $nombreEquipo,
                    'categoria_id' => $categoriaId,
                ]);

                foreach ($jugadoresEquipo as $orden => $jugador) {
                    $equipo->jugadores()->attach($jugador->id, ['orden' => $orden + 1]);
                }

                $this->command->info("✓ Equipo '{$nombreEquipo}' creado (Categoría {$categoriaNombre})");
            }

            // 8. Crear cuarto torneo: FÚTBOL - Liga
            $this->command->info('');
            $this->command->info('===========================================');
            $this->command->info('Creando cuarto torneo: PICKLEBALL - Liga (Categorías)');
            $this->command->info('===========================================');

            $categoriasPickleball = Categoria::where('deporte_id', $deporte->id)->get();

            $torneoFutbolLiga = Torneo::create([
                'nombre' => 'Torneo de Pickleball Categorías - Apertura 2025',
                'deporte_id' => $deporte->id,
                'descripcion' => 'Liga de Pickleball todos contra todos. ¡La tabla nunca miente!',
                'fecha_inicio' => now()->addDays(50),
                'fecha_fin' => now()->addDays(80),
                'fecha_limite_inscripcion' => now()->addDays(45),
                'premios' => "1er Puesto: $100,000\n2do Puesto: $60,000\n3er Puesto: $30,000",
                'complejo_id' => $complejo->id,
                'organizador_id' => $organizador->id,
                'precio_inscripcion' => 8000,
                'formato_id' => $formatoLiga->id,
                'estado' => 'activo',
            ]);

            $this->command->info("✓ Torneo '{$torneoFutbolLiga->nombre}' creado");

            // Crear pago PAGADO
            PagoTorneo::create([
                'torneo_id' => $torneoFutbolLiga->id,
                'organizador_id' => $organizador->id,
                'monto' => $precioTorneo,
                'estado' => 'pagado',
                'es_primer_torneo_gratis' => false,
                'referencia_pago' => 'SEED_' . uniqid(),
                'metodo_pago' => 'mercadopago',
                'pagado_en' => now()->subDays(3),
                'notas' => 'Pago simulado por seeder',
            ]);

            // Incrementar contador de torneos creados
            $organizador->increment('torneos_creados');

            $this->command->info("✓ Pago registrado como pagado (\${$precioTorneo})");

            // Asignar categoría Libre con 8 equipos
            $categoriaLibre = $categoriasPickleball->where('nombre', 'Libre')->first();

            $torneoFutbolLiga->categorias()->attach([
                $categoriaLibre->id => [
                    'cupos_categoria' => 8,
                    'numero_grupos' => null,
                    'tamanio_grupo_id' => null,
                    'avance_grupos_id' => null,
                ],
            ]);

            $this->command->info("✓ Categoría asignada:");
            $this->command->info("  - Libre: 8 equipos (28 partidos)");

            // Crear equipos para Liga de Fútbol
            $equiposFutbolLiga = [
                'Los Cracks FC',
                'River Plate Jr',
                'Boca Juniors Jr',
                'Independiente FC',
                'Racing Club FC',
                'San Lorenzo FC',
                'Huracán FC',
                'Vélez Sarsfield FC',
            ];

            foreach ($equiposFutbolLiga as $nombreEquipo) {
                $equipo = $torneoFutbolLiga->equipos()->create([
                    'nombre' => $nombreEquipo,
                    'categoria_id' => $categoriaLibre->id,
                ]);

                // Crear 11 jugadores por equipo (formación básica)
                for ($i = 1; $i <= 11; $i++) {
                    $jugador = Jugador::create([
                        'nombre' => 'Jugador ' . $i,
                        'apellido' => $nombreEquipo,
                        'dni' => rand(20000000, 45000000),
                        'telefono' => '+54 9 11 ' . rand(1000, 9999) . '-' . rand(1000, 9999),
                        'organizador_id' => $organizador->id,
                    ]);

                    $equipo->jugadores()->attach($jugador->id, ['orden' => $i]);
                }

                $this->command->info("✓ Equipo '{$nombreEquipo}' creado con 11 jugadores");
            }

            // 9. Crear quinto torneo: FÚTBOL - Eliminación Directa
            $this->command->info('');
            $this->command->info('===========================================');
            $this->command->info('Creando quinto torneo: PICKLEBALL - Eliminación Directa');
            $this->command->info('===========================================');

            $torneoFutbolED = Torneo::create([
                'nombre' => 'Copa Pickleball Knockout - Eliminación Directa 2025',
                'deporte_id' => $deporte->id,
                'descripcion' => 'Copa de Pickleball en formato eliminación directa. ¡Ganas o te vas!',
                'fecha_inicio' => now()->addDays(60),
                'fecha_fin' => now()->addDays(70),
                'fecha_limite_inscripcion' => now()->addDays(55),
                'premios' => "1er Puesto: $150,000\n2do Puesto: $80,000\n3er Puesto: $40,000",
                'complejo_id' => $complejo->id,
                'organizador_id' => $organizador->id,
                'precio_inscripcion' => 10000,
                'formato_id' => $formatoED->id,
                'estado' => 'activo',
            ]);

            $this->command->info("✓ Torneo '{$torneoFutbolED->nombre}' creado");

            // Crear pago PAGADO
            PagoTorneo::create([
                'torneo_id' => $torneoFutbolED->id,
                'organizador_id' => $organizador->id,
                'monto' => $precioTorneo,
                'estado' => 'pagado',
                'es_primer_torneo_gratis' => false,
                'referencia_pago' => 'SEED_' . uniqid(),
                'metodo_pago' => 'mercadopago',
                'pagado_en' => now()->subDays(1),
                'notas' => 'Pago simulado por seeder',
            ]);

            // Incrementar contador de torneos creados
            $organizador->increment('torneos_creados');

            $this->command->info("✓ Pago registrado como pagado (\${$precioTorneo})");

            // Asignar categoría Libre con 8 equipos (para tener 3 rondas)
            $torneoFutbolED->categorias()->attach([
                $categoriaLibre->id => [
                    'cupos_categoria' => 8,
                    'numero_grupos' => null,
                    'tamanio_grupo_id' => null,
                    'avance_grupos_id' => null,
                ],
            ]);

            $this->command->info("✓ Categoría asignada:");
            $this->command->info("  - Libre: 8 equipos (3 rondas)");

            // Crear equipos para Eliminación Directa de Fútbol
            $equiposFutbolED = [
                'Atlético Madrid FC',
                'Real Madrid Jr',
                'Barcelona FC Jr',
                'Bayern Munich FC',
                'Manchester United FC',
                'Liverpool FC',
                'Juventus FC',
                'Inter Milan FC',
            ];

            foreach ($equiposFutbolED as $nombreEquipo) {
                $equipo = $torneoFutbolED->equipos()->create([
                    'nombre' => $nombreEquipo,
                    'categoria_id' => $categoriaLibre->id,
                ]);

                // Crear 11 jugadores por equipo
                for ($i = 1; $i <= 11; $i++) {
                    $jugador = Jugador::create([
                        'nombre' => 'Jugador ' . $i,
                        'apellido' => $nombreEquipo,
                        'dni' => rand(20000000, 45000000),
                        'telefono' => '+54 9 11 ' . rand(1000, 9999) . '-' . rand(1000, 9999),
                        'organizador_id' => $organizador->id,
                    ]);

                    $equipo->jugadores()->attach($jugador->id, ['orden' => $i]);
                }

                $this->command->info("✓ Equipo '{$nombreEquipo}' creado con 11 jugadores");
            }

            DB::commit();

            $this->command->info('');
            $this->command->info('===========================================');
            $this->command->info('✓ Seeder completado exitosamente');
            $this->command->info('===========================================');
            $this->command->info('');
            $this->command->info('TORNEO 1 (PICKLEBALL - Fase de Grupos + Eliminación):');
            $this->command->info("  Nombre: {$torneo->nombre}");
            $this->command->info("  Total Equipos: {$torneo->equipos()->count()}");
            $this->command->info('  Distribución por categoría:');
            $this->command->info('    - Masculino: 12 equipos en 4 grupos de 3');
            $this->command->info('    - Femenino: 12 equipos en 3 grupos de 4');
            $this->command->info('    - Mixto: 10 equipos en 2 grupos de 5');
            $this->command->info('  Total grupos: 9 (4 + 3 + 2)');
            $this->command->info('  Total cupos: 34 (12 + 12 + 10)');
            $this->command->info('  💰 PAGO: GRATUITO (primer torneo)');
            $this->command->info('  🏆 ESTADO: activo');
            $this->command->info('');
            $this->command->info('TORNEO 2 (PICKLEBALL - Liga):');
            $this->command->info("  Nombre: {$torneoRR->nombre}");
            $this->command->info("  Total Equipos: {$torneoRR->equipos()->count()}");
            $this->command->info('  Distribución por categoría:');
            $this->command->info('    - Masculino: 6 equipos (15 partidos)');
            $this->command->info('    - Femenino: 8 equipos (28 partidos)');
            $this->command->info('    - Mixto: 6 equipos (15 partidos)');
            $this->command->info('  Total cupos: 20 (6 + 8 + 6)');
            $this->command->info('  Total partidos: 58 (15 + 28 + 15)');
            $this->command->info("  💰 PAGO: PAGADO (\${$precioTorneo})");
            $this->command->info('  🏆 ESTADO: activo');
            $this->command->info('');
            $this->command->info('TORNEO 3 (PICKLEBALL - Eliminación Directa):');
            $this->command->info("  Nombre: {$torneoED->nombre}");
            $this->command->info("  Total Equipos: {$torneoED->equipos()->count()}");
            $this->command->info('  Distribución por categoría:');
            $this->command->info('    - Masculino: 8 equipos (3 rondas)');
            $this->command->info('    - Femenino: 16 equipos (4 rondas)');
            $this->command->info('    - Mixto: 4 equipos (2 rondas)');
            $this->command->info('  Total cupos: 28 (8 + 16 + 4)');
            $this->command->info('  Total rondas máximas: 9 (3 + 4 + 2)');
            $this->command->info("  💰 PAGO: PAGADO (\${$precioTorneo})");
            $this->command->info('  🏆 ESTADO: activo');
            $this->command->info('');
            $this->command->info('TORNEO 4 (PICKLEBALL - Liga Categorías):');
            $this->command->info("  Nombre: {$torneoFutbolLiga->nombre}");
            $this->command->info("  Total Equipos: {$torneoFutbolLiga->equipos()->count()}");
            $this->command->info('  Distribución:');
            $this->command->info('    - Libre: 8 equipos (28 partidos)');
            $this->command->info('    - 11 jugadores por equipo');
            $this->command->info('  Total jugadores de fútbol: 88');
            $this->command->info("  💰 PAGO: PAGADO (\${$precioTorneo})");
            $this->command->info('  🏆 ESTADO: activo');
            $this->command->info('');
            $this->command->info('TORNEO 5 (PICKLEBALL - Eliminación Directa Categorías):');
            $this->command->info("  Nombre: {$torneoFutbolED->nombre}");
            $this->command->info("  Total Equipos: {$torneoFutbolED->equipos()->count()}");
            $this->command->info('  Distribución:');
            $this->command->info('    - Libre: 8 equipos (3 rondas)');
            $this->command->info('    - 11 jugadores por equipo');
            $this->command->info('  Total jugadores de fútbol: 88');
            $this->command->info("  💰 PAGO: PAGADO (\${$precioTorneo})");
            $this->command->info('  🏆 ESTADO: activo');
            $this->command->info('');
            $this->command->info("Total Jugadores de Pickleball: {$jugadores->count()}");
            $this->command->info("Total Jugadores adicionales: 176");
            $this->command->info("Torneos creados por organizador: {$organizador->fresh()->torneos_creados}");
            $this->command->info('===========================================');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generar nombre de equipo a partir de los apellidos de los jugadores
     */
    private function generarNombreEquipo(array $jugadores): string
    {
        $apellidos = array_map(fn($jugador) => $jugador->apellido, $jugadores);
        return implode(' / ', $apellidos);
    }

    /**
     * Crear jugadores de ejemplo
     */
    private function crearJugadores()
    {
        // Obtener organizador para asignar los jugadores
        $organizador = User::role('organizador')->first();

        $nombres = [
            // Jugadores 1-24 (Categoría 7ma - 12 equipos)
            ['Juan', 'González'], ['Carlos', 'Martínez'], ['Diego', 'Fernández'], ['Miguel', 'López'],
            ['Pedro', 'Rodríguez'], ['Luis', 'Pérez'], ['Jorge', 'García'], ['Andrés', 'Sánchez'],
            ['Fernando', 'Romero'], ['Pablo', 'Torres'], ['Javier', 'Díaz'], ['Roberto', 'Ramírez'],
            ['Alberto', 'Vega'], ['Ricardo', 'Castro'], ['Martín', 'Morales'], ['Sebastián', 'Ruiz'],
            ['Gabriel', 'Herrera'], ['Daniel', 'Molina'], ['Alejandro', 'Ortiz'], ['Nicolás', 'Silva'],
            ['Facundo', 'Méndez'], ['Matías', 'Navarro'], ['Santiago', 'Campos'], ['Joaquín', 'Ramos'],

            // Jugadores 25-48 (Categoría 6ta - 12 equipos)
            ['Emiliano', 'Vargas'], ['Luciano', 'Luna'], ['Tomás', 'Flores'], ['Agustín', 'Guzmán'],
            ['Ignacio', 'Cabrera'], ['Maximiliano', 'Ríos'], ['Franco', 'Domínguez'], ['Gonzalo', 'Aguilar'],
            ['Valentín', 'Blanco'], ['Bruno', 'Mendoza'], ['Thiago', 'Suárez'], ['Lorenzo', 'Giménez'],
            ['Felipe', 'Medina'], ['Bautista', 'Benítez'], ['Lautaro', 'Acosta'], ['Benjamín', 'Ponce'],
            ['Mateo', 'Rojas'], ['Lucas', 'Carrillo'], ['Simón', 'Cortés'], ['Marcos', 'Fuentes'],
            ['Dante', 'Paredes'], ['Ian', 'Salazar'], ['Julián', 'Bravo'], ['Adrián', 'Ibáñez'],

            // Jugadores 49-68 (Categoría 5ta - 10 equipos)
            ['Ezequiel', 'Lara'], ['Cristian', 'Maldonado'], ['Damián', 'Núñez'], ['Rodrigo', 'Estrada'],
            ['Leandro', 'Sandoval'], ['Jose', 'Leyva'], ['Claudio', 'Cárdenas'], ['Gustavo', 'Montoya'],
            ['Sergio', 'Guerrero'], ['Oscar', 'Mejía'], ['Esteban', 'Espinoza'], ['Ramiro', 'León'],
            ['Nicolás', 'Reyes'], ['Hernán', 'Pacheco'], ['Walter', 'Cervantes'], ['Fabián', 'Duarte'],
            ['Rubén', 'Galván'], ['Ariel', 'Ochoa'], ['Marcelo', 'Villarreal'], ['Julio', 'Sosa'],
        ];

        $jugadores = collect();

        foreach ($nombres as [$nombre, $apellido]) {
            $jugador = Jugador::create([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'dni' => rand(20000000, 45000000),
                'telefono' => '+54 9 11 ' . rand(1000, 9999) . '-' . rand(1000, 9999),
                'organizador_id' => $organizador->id, // Asignar al organizador
            ]);

            if($jugador->apellido === 'Leyva') {
                $jugador->email = 'josealeleyva16@gmail.com';
                $jugador->save();
            }

            $jugadores->push($jugador);
        }

        return $jugadores;
    }
}
