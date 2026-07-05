<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::insert([
            [
                'name' => 'Alimentación',
                'type' => 'expense',
                'icon' => '🍔',
                'color' => '#FF9800',
            ],
            [
                'name' => 'Transporte',
                'type' => 'expense',
                'icon' => '🚗',
                'color' => '#2196F3',
            ],
            [
                'name' => 'Vivienda',
                'type' => 'expense',
                'icon' => '🏠',
                'color' => '#795548',
            ],
            [
                'name' => 'Ocio',
                'type' => 'expense',
                'icon' => '🎮',
                'color' => '#9C27B0',
            ],
            [
                'name' => 'Salario',
                'type' => 'income',
                'icon' => '💼',
                'color' => '#4CAF50',
            ],
            [
                'name' => 'Inversiones',
                'type' => 'income',
                'icon' => '📈',
                'color' => '#009688',
            ],
        ]);
    }
}
