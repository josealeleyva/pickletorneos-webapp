<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener organizador con id 2
        $organizadorId = 2;

        // Obtener IDs de deportes
        $padelId = DB::table('deportes')->where('nombre', 'Padel')->first()->id;
        $futbolId = DB::table('deportes')->where('nombre', 'Futbol')->first()->id;
        $tenisId = DB::table('deportes')->where('nombre', 'Tenis')->first()->id;

        $categorias = [
            // Categorías de Padel
            ['deporte_id' => $padelId, 'organizador_id' => $organizadorId, 'nombre' => '8va', 'created_at' => now(), 'updated_at' => now()],
            ['deporte_id' => $padelId, 'organizador_id' => $organizadorId, 'nombre' => '7ma', 'created_at' => now(), 'updated_at' => now()],
            ['deporte_id' => $padelId, 'organizador_id' => $organizadorId, 'nombre' => '6ta', 'created_at' => now(), 'updated_at' => now()],
            ['deporte_id' => $padelId, 'organizador_id' => $organizadorId, 'nombre' => '5ta', 'created_at' => now(), 'updated_at' => now()],
            ['deporte_id' => $padelId, 'organizador_id' => $organizadorId, 'nombre' => '4ta', 'created_at' => now(), 'updated_at' => now()],
            ['deporte_id' => $padelId, 'organizador_id' => $organizadorId, 'nombre' => '3ra', 'created_at' => now(), 'updated_at' => now()],
            ['deporte_id' => $padelId, 'organizador_id' => $organizadorId, 'nombre' => '2da', 'created_at' => now(), 'updated_at' => now()],
            ['deporte_id' => $padelId, 'organizador_id' => $organizadorId, 'nombre' => '1ra', 'created_at' => now(), 'updated_at' => now()],
            ['deporte_id' => $padelId, 'organizador_id' => $organizadorId, 'nombre' => 'Libre', 'created_at' => now(), 'updated_at' => now()],

            // Categorías de Futbol
            ['deporte_id' => $futbolId, 'organizador_id' => $organizadorId, 'nombre' => 'Libre', 'created_at' => now(), 'updated_at' => now()],
            ['deporte_id' => $futbolId, 'organizador_id' => $organizadorId, 'nombre' => '+30', 'created_at' => now(), 'updated_at' => now()],
            ['deporte_id' => $futbolId, 'organizador_id' => $organizadorId, 'nombre' => '+35', 'created_at' => now(), 'updated_at' => now()],
            ['deporte_id' => $futbolId, 'organizador_id' => $organizadorId, 'nombre' => '+40', 'created_at' => now(), 'updated_at' => now()],
            ['deporte_id' => $futbolId, 'organizador_id' => $organizadorId, 'nombre' => '+45', 'created_at' => now(), 'updated_at' => now()],
            ['deporte_id' => $futbolId, 'organizador_id' => $organizadorId, 'nombre' => 'Veteranos', 'created_at' => now(), 'updated_at' => now()],

            // Categorías de Tenis
            ['deporte_id' => $tenisId, 'organizador_id' => $organizadorId, 'nombre' => 'Libre', 'created_at' => now(), 'updated_at' => now()],
            ['deporte_id' => $tenisId, 'organizador_id' => $organizadorId, 'nombre' => 'A', 'created_at' => now(), 'updated_at' => now()],
            ['deporte_id' => $tenisId, 'organizador_id' => $organizadorId, 'nombre' => 'B', 'created_at' => now(), 'updated_at' => now()],
            ['deporte_id' => $tenisId, 'organizador_id' => $organizadorId, 'nombre' => 'C', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('categorias')->insert($categorias);
    }
}
