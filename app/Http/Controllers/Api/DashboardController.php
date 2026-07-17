<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
{
    $user = $request->user();

    $income = $user->transactions()
        ->where('type', 'income')
        ->sum('amount');

    $expense = $user->transactions()
        ->where('type', 'expense')
        ->sum('amount');

    $balance = $income - $expense;

    $goalsCount = $user->savingGoals()->count();

    $mainGoal = $user->savingGoals()
        ->latest()
        ->first();

    $latestTransactions = $user->transactions()
        ->latest()
        ->take(5)
        ->get();

    $recommendations = [];

    /*
    |--------------------------------------------------------------------------
    | Balance
    |--------------------------------------------------------------------------
    */

    if ($balance < 0) {

        $recommendations[] =
            "⚠️ Tus gastos superan tus ingresos. Revisa tus últimos movimientos.";

    } elseif ($balance > 0) {

        $recommendations[] =
            "💰 Tienes un balance positivo. Sigue manteniendo este ritmo.";
    }

    /*
    |--------------------------------------------------------------------------
    | Ahorro
    |--------------------------------------------------------------------------
    */

    if ($income > 0) {

        $savingRate =
            (($income - $expense) / $income) * 100;

        if ($savingRate >= 20) {

            $recommendations[] =
                "🎯 Estás ahorrando más del 20% de tus ingresos.";

        } elseif ($savingRate > 0) {

            $recommendations[] =
                "💡 Intenta aumentar ligeramente tu ahorro mensual.";

        } else {

            $recommendations[] =
                "🚨 Actualmente no estás generando ahorro.";
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Objetivo principal
    |--------------------------------------------------------------------------
    */

    if ($mainGoal) {

        $progress = 0;

        if ($mainGoal->target_amount > 0) {

            $progress =
                ($mainGoal->current_amount /
                    $mainGoal->target_amount) * 100;
        }

        if ($progress >= 100) {

            $recommendations[] =
                "🏆 Has completado tu objetivo '{$mainGoal->title}'.";

        } elseif ($progress >= 75) {

            $recommendations[] =
                "🔥 Ya has completado más del 75% de tu objetivo '{$mainGoal->title}'.";

        } elseif ($progress >= 50) {

            $recommendations[] =
                "🚀 Vas por la mitad de tu objetivo '{$mainGoal->title}'.";
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Sin objetivos
    |--------------------------------------------------------------------------
    */

    if ($goalsCount === 0) {

        $recommendations[] =
            "🎯 Crea un objetivo de ahorro para mejorar tu planificación financiera.";
    }

    return response()->json([
        'balance' => (float) $balance,
        'income' => (float) $income,
        'expense' => (float) $expense,
        'goals' => $goalsCount,
        'mainGoal' => $mainGoal,
        'latestTransactions' => $latestTransactions,
        'recommendations' => $recommendations,
    ]);
}
}