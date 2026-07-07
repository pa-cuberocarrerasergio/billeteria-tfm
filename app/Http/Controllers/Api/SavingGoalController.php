<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SavingGoal;
use Illuminate\Http\Request;
use App\Models\Achievement;

class SavingGoalController extends Controller
{
    public function index()
    {
        return response()->json(
            SavingGoal::all()
        );
    }

    public function show(SavingGoal $savingGoal)
    {
        return response()->json($savingGoal);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'required|numeric|min:0',
            'target_date' => 'required|date',
            'priority' => 'required|in:high,medium,low',
            'emoji' => 'nullable|string|max:10',
        ]);

        $goal = SavingGoal::create($validated);
        $achievement = Achievement::find(2);

        if (
            $achievement &&
            !$goal->user->achievements()->where('achievement_id', 2)->exists()
        ) {
            $goal->user->achievements()->attach(2);
        }

        return response()->json($goal, 201);
    }

    public function update(Request $request, SavingGoal $savingGoal)
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

        $savingGoal->update($validated);

        return response()->json($savingGoal);
    }

    public function destroy(SavingGoal $savingGoal)
    {
        $savingGoal->delete();

        return response()->json([
            'message' => 'Saving goal deleted successfully'
        ]);
    }
}