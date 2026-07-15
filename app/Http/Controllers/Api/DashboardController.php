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

        return response()->json([
            'balance' => (float) $balance,
            'income' => (float) $income,
            'expense' => (float) $expense,
            'goals' => $goalsCount,

            'mainGoal' => $mainGoal,

            'latestTransactions' => $latestTransactions,
        ]);
    }
}