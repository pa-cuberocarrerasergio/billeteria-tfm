<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Achievement;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        Achievement::insert([

            [
                'title' => 'Primer gasto',
                'description' => 'Has registrado tu primer gasto.',
                'icon' => '💸',
            ],

            [
                'title' => 'Primer ahorro',
                'description' => 'Has creado tu primer objetivo de ahorro.',
                'icon' => '🎯',
            ],

            [
                'title' => 'Ahorrador principiante',
                'description' => 'Has ahorrado 100€.',
                'icon' => '💰',
            ],

            [
                'title' => 'Constancia',
                'description' => 'Has utilizado BilleterIA durante 7 días.',
                'icon' => '📈',
            ],

            [
                'title' => 'Ahorrador experto',
                'description' => 'Has ahorrado 500€.',
                'icon' => '🏦',
            ],

            [
                'title' => 'Organizado',
                'description' => 'Has registrado 10 transacciones.',
                'icon' => '📊',
            ],

            [
                'title' => 'Objetivo cumplido',
                'description' => 'Has completado un objetivo de ahorro.',
                'icon' => '🏆',
            ],

        ]);
    }
}