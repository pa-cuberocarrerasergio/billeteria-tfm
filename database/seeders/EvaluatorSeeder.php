<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\SavingGoal;
use App\Models\Transaction;
use App\Models\CoachPreference;
use Illuminate\Support\Facades\Hash;

class EvaluatorSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear o actualizar usuario Evaluador
        $user = User::updateOrCreate(
            ['email' => 'profesor@billeteria.com'],
            [
                'nickname' => 'Profesor Evaluador',
                'password' => Hash::make('evaluador123'),
            ]
        );

        // 2. Objetivos de ahorro de ejemplo
        $goal1 = SavingGoal::updateOrCreate(
            ['user_id' => $user->id, 'title' => 'Viaje a Japón ✈️'],
            [
                'description' => 'Ahorro para vacaciones de verano en Tokio y Kioto',
                'target_amount' => 3000.00,
                'current_amount' => 1500.00,
                'target_date' => date('Y-m-d', strtotime('+6 months')),
                'priority' => 'high',
                'emoji' => '🌴',
            ]
        );

        $goal2 = SavingGoal::updateOrCreate(
            ['user_id' => $user->id, 'title' => 'Coche Nuevo 🚗'],
            [
                'description' => 'Entrada para vehículo híbrido',
                'target_amount' => 5000.00,
                'current_amount' => 1200.00,
                'target_date' => date('Y-m-d', strtotime('+12 months')),
                'priority' => 'medium',
                'emoji' => '🚗',
            ]
        );

        $goal3 = SavingGoal::updateOrCreate(
            ['user_id' => $user->id, 'title' => 'Fondo de Emergencia 🛡️'],
            [
                'description' => 'Colchón financiero de 3 meses de gastos',
                'target_amount' => 2000.00,
                'current_amount' => 800.00,
                'target_date' => date('Y-m-d', strtotime('+3 months')),
                'priority' => 'low',
                'emoji' => '🎯',
            ]
        );

        // Categorías
        $incomeCategory = Category::where('name', 'Salario')->first();
        $foodCategory = Category::where('name', 'Alimentación')->first();
        $housingCategory = Category::where('name', 'Vivienda')->first();
        $leisureCategory = Category::where('name', 'Ocio')->first();

        // 3. Transacciones de ejemplo si no existen
        if ($user->transactions()->count() === 0) {
            Transaction::create([
                'user_id' => $user->id,
                'category_id' => $incomeCategory?->id,
                'type' => 'income',
                'title' => 'Nómina Mensual',
                'description' => 'Ingreso de salario base',
                'amount' => 2500.00,
                'transaction_date' => date('Y-m-d'),
            ]);

            Transaction::create([
                'user_id' => $user->id,
                'category_id' => $housingCategory?->id,
                'type' => 'expense',
                'title' => 'Alquiler Piso',
                'description' => 'Pago mensual vivienda',
                'amount' => 750.00,
                'transaction_date' => date('Y-m-d', strtotime('-5 days')),
            ]);

            Transaction::create([
                'user_id' => $user->id,
                'category_id' => $foodCategory?->id,
                'type' => 'expense',
                'title' => 'Compra Mercadona',
                'description' => 'Supermercado quincenal',
                'amount' => 120.50,
                'transaction_date' => date('Y-m-d', strtotime('-3 days')),
            ]);

            Transaction::create([
                'user_id' => $user->id,
                'category_id' => $leisureCategory?->id,
                'type' => 'expense',
                'title' => 'Suscripción Netflix & Spotify',
                'description' => 'Servicios digitales',
                'amount' => 25.00,
                'transaction_date' => date('Y-m-d', strtotime('-1 day')),
            ]);
        }

        // 4. Preferencias del Coach IA para el Evaluador
        CoachPreference::updateOrCreate(
            ['user_id' => $user->id],
            [
                'preferred_name' => 'Profesor',
                'conversation_style' => 'cercano',
                'coach_background' => 'Profesor universitario evaluador del TFM',
            ]
        );
    }
}
