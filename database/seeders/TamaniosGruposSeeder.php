<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TamaniosGruposSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tamanios = [
            ['tamanio' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['tamanio' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['tamanio' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['tamanio' => 6, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('tamanios_grupos')->insert($tamanios);
    }
}
