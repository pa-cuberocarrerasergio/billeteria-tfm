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
        $categories = [
            ['name' => 'Alimentación', 'type' => 'expense', 'icon' => '🍔', 'color' => '#FF9800'],
            ['name' => 'Transporte', 'type' => 'expense', 'icon' => '🚗', 'color' => '#2196F3'],
            ['name' => 'Vivienda', 'type' => 'expense', 'icon' => '🏠', 'color' => '#795548'],
            ['name' => 'Ocio', 'type' => 'expense', 'icon' => '🎮', 'color' => '#9C27B0'],
            ['name' => 'Suscripciones', 'type' => 'expense', 'icon' => '🍿', 'color' => '#8B5CF6'],
            ['name' => 'Salud', 'type' => 'expense', 'icon' => '💊', 'color' => '#06B6D4'],
            ['name' => 'Otros gastos', 'type' => 'expense', 'icon' => '📦', 'color' => '#94A3B8'],

            ['name' => 'Salario', 'type' => 'income', 'icon' => '💼', 'color' => '#4CAF50'],
            ['name' => 'Inversiones', 'type' => 'income', 'icon' => '📈', 'color' => '#009688'],
            ['name' => 'Freelance', 'type' => 'income', 'icon' => '💻', 'color' => '#3B82F6'],
            ['name' => 'Otros ingresos', 'type' => 'income', 'icon' => '💰', 'color' => '#10B981'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['name' => $cat['name'], 'type' => $cat['type']],
                $cat
            );
        }
    }
}
