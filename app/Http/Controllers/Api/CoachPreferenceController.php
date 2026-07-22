<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CoachPreference;
use App\Models\User;
use Illuminate\Http\Request;

class CoachPreferenceController extends Controller
{
    public function show(User $user)
    {
        return response()->json(
            CoachPreference::where('user_id', $user->id)->first()
        );
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'preferred_name' => 'nullable|string|max:255',
            'conversation_style' => 'required|in:formal,cercano,motivador',
            'coach_background' => 'nullable|string|max:255',
        ]);

        $preference = CoachPreference::updateOrCreate(
            ['user_id' => $user->id],
            [
                'preferred_name' => $validated['preferred_name'] ?? null,
                'conversation_style' => $validated['conversation_style'],
                'coach_background' => $validated['coach_background'] ?? 'default',
            ]
        );

        return response()->json($preference);
    }
}
