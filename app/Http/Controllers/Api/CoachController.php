<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\CoachMessage;

class CoachController extends Controller
{
    public function chat(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = $request->user();

        $transactions = $user->transactions()
            ->latest()
            ->take(10)
            ->get();

        $goals = $user->savingGoals()->get();

        $preference = $user->coachPreference;

        $context = "Eres el coach financiero de BilleterIA.\n\n";

        if ($preference) {
            $context .= "Estilo de conversación: "
                . ($preference->conversation_style ?? 'motivador')
                . "\n";

            $context .= "Contexto personal: "
                . ($preference->coach_background ?? '')
                . "\n\n";
        }

        $context .= "Objetivos de ahorro:\n";

        if ($goals->count()) {
            foreach ($goals as $goal) {
                $context .=
                    "- {$goal->title}\n" .
                    "  Objetivo: {$goal->target_amount}€\n" .
                    "  Ahorrado: {$goal->current_amount}€\n";
            }
        } else {
            $context .= "- No tiene objetivos de ahorro registrados.\n";
        }

        $context .= "\nÚltimas transacciones:\n";

        if ($transactions->count()) {
            foreach ($transactions as $transaction) {
                $context .=
                    "- {$transaction->title}: {$transaction->amount}€ ({$transaction->type})\n";
            }
        } else {
            $context .= "- No tiene transacciones registradas.\n";
        }

        $prompt = $context .
            "\nPregunta del usuario:\n" .
            $validated['message'];

        $apiKey = config('services.gemini.api_key');

        $response = Http::post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key={$apiKey}",
            [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ]
            ]
        );

       $reply = $response->json(
            'candidates.0.content.parts.0.text'
        );
        
        CoachMessage::create([
            'user_id' => $user->id,
            'message' => $validated['message'],
            'response' => $reply,
        ]);

        return response()->json([
            'reply' => $reply,
        ]);
    }

    public function history(Request $request)
    {
        return response()->json(
            $request->user()
                ->coachMessages()
                ->latest()
                ->get()
        );
    }
}