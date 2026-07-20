<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Achievement;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            $request->user()
                ->transactions()
                ->with('category')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:income,expense',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
        ]);

        $user = $request->user();

        $transaction = $user->transactions()->create($validated);

        $achievement = Achievement::find(1);

        if (
            $achievement &&
            !$user->achievements()
                ->where('achievement_id', 1)
                ->exists()
        ) {
            $user->achievements()->attach(1);
        }

        $transactionsCount = $user
            ->transactions()
            ->count();

        if (
            $transactionsCount >= 10 &&
            !$user->achievements()
                ->where('achievement_id', 6)
                ->exists()
        ) {
            $user->achievements()->attach(6);
        }

        return response()->json($transaction, 201);
    }

    public function show(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== $request->user()->id) {
            abort(403);
        }

        return response()->json(
            $transaction->load('category')
        );
    }

    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:income,expense',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
        ]);

        $transaction->update($validated);

        return response()->json($transaction);
    }

    public function destroy(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== $request->user()->id) {
            abort(403);
        }

        $transaction->delete();

        return response()->json([
            'message' => 'Transaction deleted successfully'
        ]);
    }
}