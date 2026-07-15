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

        $goals = $user->savingGoals()->count();

        return response()->json([
            'balance' => $balance,
            'income' => $income,
            'expense' => $expense,
            'goals' => $goals,
        ]);
    }
}
