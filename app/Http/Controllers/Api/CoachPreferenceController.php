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
            'conversation_style' => 'required|in:formal,cercano,motivador',
            'coach_background' => 'required|string|max:255',
        ]);

        $preference = CoachPreference::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return response()->json($preference);
    }
}
