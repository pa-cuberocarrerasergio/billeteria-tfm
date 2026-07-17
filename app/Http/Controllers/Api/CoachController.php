<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CoachMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
            ->take(3)
            ->get();

        $goals = $user->savingGoals()->get();

        $preference = $user->coachPreference;

        $history = $user
            ->coachMessages()
            ->latest()
            ->take(5)
            ->get()
            ->reverse();

        $totalIncome = $user->transactions()
            ->where('type', 'income')
            ->sum('amount');

        $totalExpense = $user->transactions()
            ->where('type', 'expense')
            ->sum('amount');

        $balance = $totalIncome - $totalExpense;

        $transactionsCount = $user->transactions()->count();

        $savingGoalsCount = $user->savingGoals()->count();

        $context = "Eres Billetín, el coach financiero de BilleterIA.\n\n";

        $context .= "Resumen financiero actual:\n";

        $context .= "- Balance: {$balance}€\n";
        $context .= "- Ingresos totales: {$totalIncome}€\n";
        $context .= "- Gastos totales: {$totalExpense}€\n";
        $context .= "- Transacciones registradas: {$transactionsCount}\n";
        $context .= "- Objetivos de ahorro: {$savingGoalsCount}\n\n";

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

                $progress = 0;

                if ($goal->target_amount > 0) {

                    $progress = round(
                        ($goal->current_amount / $goal->target_amount) * 100,
                        2
                    );
                }

                $context .=
                    "- {$goal->title}\n" .
                    "  Objetivo: {$goal->target_amount}€\n" .
                    "  Ahorrado: {$goal->current_amount}€\n" .
                    "  Progreso: {$progress}%\n";
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

        $context .= "\nHistorial reciente:\n";

        foreach ($history as $item) {

            $context .=
                "Usuario: {$item->message}\n" .
                "Billetín: {$item->response}\n";
        }

        $context .= "

Instrucciones:

- Eres Billetín, el coach financiero de BilleterIA.
- Responde SIEMPRE en español.
- Responde primero a la pregunta concreta del usuario.
- No sigas una plantilla fija.
- No repitas siempre el balance, gastos u objetivos.
- Utiliza los datos financieros solo cuando sean relevantes.
- Habla de forma natural y conversacional.
- Sé cercano, útil y motivador.
- Si el usuario hace una pregunta específica, contéstala directamente.
- Máximo 120 palabras.
- Puedes usar emojis cuando aporten valor.

IMPORTANTE:

Devuelve SIEMPRE un JSON válido.

Formato:

{
    \"reply\": \"respuesta para el usuario\",
    \"mood\": \"normal\"
}

Estados permitidos:

- normal
- happy
- thinking
- worried

Reglas:

- happy => si el usuario mejora sus finanzas, ahorra o cumple objetivos.
- worried => si detectas problemas financieros, gastos elevados o balance negativo.
- thinking => si la respuesta requiere análisis o cálculo.
- normal => para cualquier otro caso.

NO escribas texto fuera del JSON.
";

        $prompt =
            $context .
            "\n\nPregunta actual del usuario:\n" .
            $validated['message'];

        $apiKey = config('services.gemini.api_key');

        $response = Http::post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent?key={$apiKey}",
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

        if (!$response->successful()) {

            return response()->json([
                'error' => $response->json()
            ], $response->status());
        }

        $content = $response->json(
            'candidates.0.content.parts.0.text'
        );

        $content = trim($content);

        $content = preg_replace(
            '/^```json|```$/m',
            '',
            $content
        );

        $decoded = json_decode(
            $content,
            true
        );

        if (
            json_last_error() === JSON_ERROR_NONE &&
            isset($decoded['reply'])
        ) {

            $reply = $decoded['reply'];

            $mood = $decoded['mood'] ?? 'normal';

        } else {

            $reply = $content;

            $mood = 'normal';
        }

        $reply = preg_replace('/\*+/', '', $reply);
        $reply = str_replace('#', '', $reply);

        CoachMessage::create([
            'user_id' => $user->id,
            'message' => $validated['message'],
            'response' => $reply,
            'mood' => $mood,
        ]);

        return response()->json([
            'reply' => $reply,
            'mood' => $mood,
        ]);
    }

    public function history(Request $request)
    {
        return response()->json(
            $request->user()
                ->coachMessages()
                ->oldest()
                ->get()
        );
    }
}