<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Achievement;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
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
]);
    }
}
