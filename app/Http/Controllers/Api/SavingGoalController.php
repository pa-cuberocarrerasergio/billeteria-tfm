<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SavingGoal;
use Illuminate\Http\Request;
use App\Models\Achievement;

class SavingGoalController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            $request->user()
                ->savingGoals()
                ->get()
        );
    }

    public function show(Request $request, SavingGoal $savingGoal)
    {
        if ($savingGoal->user_id !== $request->user()->id) {
            abort(403);
        }

        return response()->json($savingGoal);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'required|numeric|min:0',
            'target_date' => 'required|date',
            'priority' => 'required|in:high,medium,low',
            'emoji' => 'nullable|string|max:10',
        ]);

        $user = $request->user();

        $goal = $user->savingGoals()->create($validated);

        $achievement = Achievement::find(2);

        if (
            $achievement &&
            !$user->achievements()
                ->where('achievement_id', 2)
                ->exists()
        ) {
            $user->achievements()->attach(2);
        }

        $totalSaved = $user
            ->savingGoals()
            ->sum('current_amount');

        if (
            $totalSaved >= 100 &&
            !$user->achievements()
                ->where('achievement_id', 3)
                ->exists()
        ) {
            $user->achievements()->attach(3);
        }

        if (
            $totalSaved >= 500 &&
            !$user->achievements()
                ->where('achievement_id', 5)
                ->exists()
        ) {
            $user->achievements()->attach(5);
        }

        return response()->json($goal, 201);
    }

    public function update(Request $request, SavingGoal $savingGoal)
    {
        if ($savingGoal->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'required|numeric|min:0',
            'target_date' => 'required|date',
            'priority' => 'required|in:high,medium,low',
            'emoji' => 'nullable|string|max:10',
        ]);

        $savingGoal->update($validated);

        $user = $request->user();

        if (
            $savingGoal->current_amount >=
            $savingGoal->target_amount &&
            !$user->achievements()
                ->where('achievement_id', 7)
                ->exists()
        ) {
            $user->achievements()->attach(7);
        }

        return response()->json($savingGoal);
    }

    public function destroy(Request $request, SavingGoal $savingGoal)
    {
        if ($savingGoal->user_id !== $request->user()->id) {
            abort(403);
        }

        $savingGoal->delete();

        return response()->json([
            'message' => 'Saving goal deleted successfully'
        ]);
    }
}