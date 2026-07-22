<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\SavingGoal;
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
            'category_id'      => 'required|exists:categories,id',
            'type'             => 'required|in:income,expense',
            'title'            => 'required|string|max:255',
            'amount'           => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'saving_goal_id'   => 'nullable|exists:saving_goals,id',
            'saving_amount'    => 'nullable|numeric|min:0.01',
        ]);

        $user = $request->user();

        $transaction = $user->transactions()->create($validated);

        // Apply savings to the chosen goal
        if (!empty($validated['saving_goal_id']) && !empty($validated['saving_amount'])) {
            $goal = SavingGoal::where('id', $validated['saving_goal_id'])
                ->where('user_id', $user->id)
                ->first();

            if ($goal) {
                $goal->increment('current_amount', $validated['saving_amount']);
            }
        }

        // Achievements
        $achievement = Achievement::find(1);
        if ($achievement && !$user->achievements()->where('achievement_id', 1)->exists()) {
            $user->achievements()->attach(1);
        }

        $transactionsCount = $user->transactions()->count();
        if ($transactionsCount >= 10 && !$user->achievements()->where('achievement_id', 6)->exists()) {
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
            'category_id'      => 'required|exists:categories,id',
            'type'             => 'required|in:income,expense',
            'title'            => 'required|string|max:255',
            'amount'           => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'saving_goal_id'   => 'nullable|exists:saving_goals,id',
            'saving_amount'    => 'nullable|numeric|min:0.01',
        ]);

        $user = $request->user();

        // Reverse the OLD saving before applying the new one
        if ($transaction->saving_goal_id && $transaction->saving_amount) {
            $oldGoal = SavingGoal::where('id', $transaction->saving_goal_id)
                ->where('user_id', $user->id)
                ->first();

            if ($oldGoal) {
                $oldGoal->decrement('current_amount', $transaction->saving_amount);
            }
        }

        $transaction->update($validated);

        // Apply the NEW saving
        if (!empty($validated['saving_goal_id']) && !empty($validated['saving_amount'])) {
            $newGoal = SavingGoal::where('id', $validated['saving_goal_id'])
                ->where('user_id', $user->id)
                ->first();

            if ($newGoal) {
                $newGoal->increment('current_amount', $validated['saving_amount']);
            }
        }

        return response()->json($transaction);
    }

    public function destroy(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== $request->user()->id) {
            abort(403);
        }

        // Reverse the saving when deleting the transaction
        if ($transaction->saving_goal_id && $transaction->saving_amount) {
            $goal = SavingGoal::where('id', $transaction->saving_goal_id)
                ->where('user_id', $request->user()->id)
                ->first();

            if ($goal) {
                $goal->decrement('current_amount', $transaction->saving_amount);
            }
        }

        $transaction->delete();

        return response()->json([
            'message' => 'Transaction deleted successfully'
        ]);
    }
}