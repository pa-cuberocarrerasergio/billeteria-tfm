<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Achievement;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Limpiar duplicados si se han creado en ejecuciones anteriores
        Achievement::whereNotIn('id', [1, 2, 3, 4, 5, 6, 7])->delete();

        // 2. Definir los 7 logros estándar
        $achievements = [
            [
                'id' => 1,
                'title' => 'Primer gasto',
                'description' => 'Has registrado tu primer gasto.',
                'icon' => '💸',
            ],
            [
                'id' => 2,
                'title' => 'Primer ahorro',
                'description' => 'Has creado tu primer objetivo de ahorro.',
                'icon' => '🎯',
            ],
            [
                'id' => 3,
                'title' => 'Ahorrador principiante',
                'description' => 'Has ahorrado 100€.',
                'icon' => '💰',
            ],
            [
                'id' => 4,
                'title' => 'Constancia',
                'description' => 'Has utilizado BilleterIA durante 7 días.',
                'icon' => '📈',
            ],
            [
                'id' => 5,
                'title' => 'Ahorrador experto',
                'description' => 'Has ahorrado 500€.',
                'icon' => '🏦',
            ],
            [
                'id' => 6,
                'title' => 'Organizado',
                'description' => 'Has registrado 10 transacciones.',
                'icon' => '📊',
            ],
            [
                'id' => 7,
                'title' => 'Objetivo cumplido',
                'description' => 'Has completado un objetivo de ahorro.',
                'icon' => '🏆',
            ],
        ];

        // 3. Crear o actualizar para evitar duplicaciones
        foreach ($achievements as $data) {
            Achievement::updateOrCreate(
                ['id' => $data['id']],
                $data
            );
        }
    }
}