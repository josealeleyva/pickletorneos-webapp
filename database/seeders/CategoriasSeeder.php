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
        $organizadorId = 2;

        $pickleballId = DB::table('deportes')->where('nombre', 'Pickleball')->first()->id;

        $categorias = [
            // Categorías de Pickleball
            ['deporte_id' => $pickleballId, 'organizador_id' => $organizadorId, 'nombre' => 'Masculino', 'created_at' => now(), 'updated_at' => now()],
            ['deporte_id' => $pickleballId, 'organizador_id' => $organizadorId, 'nombre' => 'Femenino', 'created_at' => now(), 'updated_at' => now()],
            ['deporte_id' => $pickleballId, 'organizador_id' => $organizadorId, 'nombre' => 'Mixto', 'created_at' => now(), 'updated_at' => now()],
            ['deporte_id' => $pickleballId, 'organizador_id' => $organizadorId, 'nombre' => 'Libre', 'created_at' => now(), 'updated_at' => now()],
            ['deporte_id' => $pickleballId, 'organizador_id' => $organizadorId, 'nombre' => '+35', 'created_at' => now(), 'updated_at' => now()],
            ['deporte_id' => $pickleballId, 'organizador_id' => $organizadorId, 'nombre' => '+40', 'created_at' => now(), 'updated_at' => now()],
            ['deporte_id' => $pickleballId, 'organizador_id' => $organizadorId, 'nombre' => '+50', 'created_at' => now(), 'updated_at' => now()],
            ['deporte_id' => $pickleballId, 'organizador_id' => $organizadorId, 'nombre' => 'Iniciación', 'created_at' => now(), 'updated_at' => now()],
            ['deporte_id' => $pickleballId, 'organizador_id' => $organizadorId, 'nombre' => 'Intermedio', 'created_at' => now(), 'updated_at' => now()],
            ['deporte_id' => $pickleballId, 'organizador_id' => $organizadorId, 'nombre' => 'Avanzado', 'created_at' => now(), 'updated_at' => now()],
            ['deporte_id' => $pickleballId, 'organizador_id' => $organizadorId, 'nombre' => 'Pro', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('categorias')->insert($categorias);
    }
}
