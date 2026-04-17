<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormatosTorneosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $formatos = [
            [
                'nombre' => 'Eliminación Directa',
                'tiene_grupos' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Fase de Grupos + Eliminación',
                'tiene_grupos' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Liga',
                'tiene_grupos' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        DB::table('formatos_torneos')->insert($formatos);
    }
}
