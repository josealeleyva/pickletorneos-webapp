<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Deporte;

class PickleballDeporteSeeder extends Seeder
{
    public function run(): void
    {
        Deporte::firstOrCreate(['nombre' => 'Pickleball']);

        $this->command->info('Deporte Pickleball agregado correctamente.');
    }
}
