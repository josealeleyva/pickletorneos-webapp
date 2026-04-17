<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Partido;
use App\Models\Juego;
use Illuminate\Support\Facades\DB;

class CargarResultadosPartidosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Carga resultados aleatorios para todos los partidos en estado 'programado'
     */
    public function run(): void
    {
        $this->command->info('Buscando partidos programados...');

        // Obtener todos los partidos en estado 'programado'
        $partidos = Partido::where('estado', 'programado')
            ->with(['equipo1', 'equipo2'])
            ->get();

        if ($partidos->isEmpty()) {
            $this->command->warn('No hay partidos en estado programado.');
            return;
        }

        $this->command->info("Se encontraron {$partidos->count()} partidos programados.");

        $partidosFinalizados = 0;

        foreach ($partidos as $partido) {
            if (!$partido->equipo1 || !$partido->equipo2) {
                $this->command->warn("Partido ID {$partido->id} no tiene ambos equipos definidos. Saltando...");
                continue;
            }

            DB::beginTransaction();
            try {
                // Generar resultados aleatorios (simula sets de pádel: mejor de 3 sets, hasta 7 juegos)
                $juegos = $this->generarResultadosPadel();

                // Calcular totales
                $sumatoriaPuntosEquipo1 = 0;
                $sumatoriaPuntosEquipo2 = 0;

                foreach ($juegos as $juego) {
                    $sumatoriaPuntosEquipo1 += $juego['juegos_equipo1'];
                    $sumatoriaPuntosEquipo2 += $juego['juegos_equipo2'];
                }

                // Determinar ganador
                $ganadorId = null;
                if ($sumatoriaPuntosEquipo1 > $sumatoriaPuntosEquipo2) {
                    $ganadorId = $partido->equipo1_id;
                } elseif ($sumatoriaPuntosEquipo2 > $sumatoriaPuntosEquipo1) {
                    $ganadorId = $partido->equipo2_id;
                }

                // Actualizar partido
                $partido->update([
                    'sets_equipo1' => $sumatoriaPuntosEquipo1,
                    'sets_equipo2' => $sumatoriaPuntosEquipo2,
                    'equipo_ganador_id' => $ganadorId,
                    'estado' => 'finalizado',
                ]);

                // Guardar juegos individuales
                foreach ($juegos as $index => $juego) {
                    Juego::create([
                        'partido_id' => $partido->id,
                        'numero_juego' => $index + 1,
                        'juegos_equipo1' => $juego['juegos_equipo1'],
                        'juegos_equipo2' => $juego['juegos_equipo2'],
                    ]);
                }

                DB::commit();
                $partidosFinalizados++;

                $ganador = $ganadorId ? ($ganadorId === $partido->equipo1_id ? $partido->equipo1->nombre : $partido->equipo2->nombre) : 'Empate';
                $this->command->info("✓ Partido {$partido->id}: {$partido->equipo1->nombre} vs {$partido->equipo2->nombre} - Ganador: {$ganador}");

            } catch (\Exception $e) {
                DB::rollBack();
                $this->command->error("✗ Error al procesar partido ID {$partido->id}: {$e->getMessage()}");
            }
        }

        $this->command->info("✓ Proceso completado: {$partidosFinalizados} partidos finalizados.");
    }

    /**
     * Genera resultados aleatorios de pádel (mejor de 3 sets, hasta 7 juegos por set)
     * @return array
     */
    private function generarResultadosPadel(): array
    {
        $juegos = [];
        $setsEquipo1 = 0;
        $setsEquipo2 = 0;

        // Jugar hasta que un equipo gane 2 sets (mejor de 3)
        while ($setsEquipo1 < 2 && $setsEquipo2 < 2) {
            // Generar resultado de un set (hasta 7 juegos, con ventaja de 2)
            $juegosEquipo1 = rand(0, 7);
            $juegosEquipo2 = rand(0, 7);

            // Asegurar que haya un ganador del set (diferencia mínima de 2 juegos o 7-6)
            if ($juegosEquipo1 === 7 && $juegosEquipo2 === 7) {
                // Tie-break: 7-6
                $juegosEquipo2 = 6;
            } elseif (abs($juegosEquipo1 - $juegosEquipo2) < 2) {
                // Ajustar para que haya diferencia de al menos 2 juegos
                if ($juegosEquipo1 > $juegosEquipo2) {
                    $juegosEquipo1 = min($juegosEquipo2 + 2, 7);
                } else {
                    $juegosEquipo2 = min($juegosEquipo1 + 2, 7);
                }
            }

            // Contar el set ganado
            if ($juegosEquipo1 > $juegosEquipo2) {
                $setsEquipo1++;
            } else {
                $setsEquipo2++;
            }

            $juegos[] = [
                'juegos_equipo1' => $juegosEquipo1,
                'juegos_equipo2' => $juegosEquipo2,
            ];
        }

        return $juegos;
    }
}
