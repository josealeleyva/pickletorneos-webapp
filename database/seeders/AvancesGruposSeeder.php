<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AvancesGruposSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $avances = [
            [
                'nombre' => 'Solo 1ros de cada grupo',
                'cantidad_avanza_directo' => 1,
                'cantidad_avanza_mejores' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => '1ros + el mejor 2do',
                'cantidad_avanza_directo' => 1,
                'cantidad_avanza_mejores' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => '1ros + los 2 mejores 2dos',
                'cantidad_avanza_directo' => 1,
                'cantidad_avanza_mejores' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => '1ros + los 3 mejores 2dos',
                'cantidad_avanza_directo' => 1,
                'cantidad_avanza_mejores' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],  
            [
                'nombre' => 'Solo 1ros y 2dos de cada grupo',
                'cantidad_avanza_directo' => 2,
                'cantidad_avanza_mejores' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => '1ros y 2dos + el mejor 3ro',
                'cantidad_avanza_directo' => 2,
                'cantidad_avanza_mejores' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => '1ros y 2dos + los 2 mejores 3ros',
                'cantidad_avanza_directo' => 2,
                'cantidad_avanza_mejores' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => '1ros y 2dos + los 3 mejores 3ros',
                'cantidad_avanza_directo' => 2,
                'cantidad_avanza_mejores' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Solo 1ros, 2dos y 3ros de cada grupo',
                'cantidad_avanza_directo' => 3,
                'cantidad_avanza_mejores' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => '1ros, 2dos y 3ros + el mejor 4to',
                'cantidad_avanza_directo' => 3,
                'cantidad_avanza_mejores' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => '1ros, 2dos y 3ros + los 2 mejores 4tos',
                'cantidad_avanza_directo' => 3,
                'cantidad_avanza_mejores' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => '1ros, 2dos y 3ros + los 3 mejores 4tos',
                'cantidad_avanza_directo' => 3,
                'cantidad_avanza_mejores' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            
        ];

        DB::table('avances_grupos')->insert($avances);
    }
}
