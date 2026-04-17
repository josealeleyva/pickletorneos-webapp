<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfiguracionPreciosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configuraciones = [
            [
                'clave' => 'porcentaje_descuento_referido',
                'valor' => '20',
                'tipo' => 'integer',
                'descripcion' => 'Porcentaje de descuento para referidos (sin el símbolo %)',
            ],
            [
                'clave' => 'porcentaje_credito_referidor',
                'valor' => '100',
                'tipo' => 'integer',
                'descripcion' => 'Porcentaje del precio del torneo que recibe el referidor como crédito (sin el símbolo %)',
            ],
        ];

        foreach ($configuraciones as $config) {
            \App\Models\ConfiguracionSistema::updateOrCreate(
                ['clave' => $config['clave']],
                $config
            );
        }

        $this->command->info('✅ Configuraciones de precios del sistema de referidos creadas correctamente.');
    }
}
