<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CoachMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CoachController extends Controller
{
    private function styleInstructions(string $style): string
    {
        return match ($style) {
            'formal' => 'Usa un tono formal, profesional y respetuoso. Trata al usuario de "usted".',
            'motivador' => 'Usa un tono motivador, enérgico y positivo. Anima al usuario a mejorar sus finanzas.',
            default => 'Usa un tono cercano, amigable y natural, como un amigo de confianza.',
        };
    }

    public function chat(Request $request)
{

    $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'userName' => 'nullable|string|max:255',
            'userTone' => 'nullable|string|in:formal,cercano,motivador,amigable',
        ]);


        $user = $request->user();

    

        $transactions = $user->transactions()
            ->latest()
            ->take(3)
            ->get();

   

        $goals = $user->savingGoals()->get();



        $preference = $user->coachPreference;



        $preferredName = $preference?->preferred_name
            ?? $validated['userName']
            ?? $user->nickname;

        $conversationStyle = $preference?->conversation_style
            ?? ($validated['userTone'] === 'amigable' ? 'cercano' : $validated['userTone'])
            ?? 'cercano';

        if (
            !$preference &&
            (!empty($validated['userName']) || !empty($validated['userTone']))
        ) {
            $user->coachPreference()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'preferred_name' => $validated['userName'] ?? $user->nickname,
                    'conversation_style' => $conversationStyle,
                    'coach_background' => 'default',
                ]
            );
        }

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

        $context .= "Nombre del usuario: {$preferredName}\n";
        $context .= "Estilo de conversación: {$conversationStyle}\n";
        $context .= $this->styleInstructions($conversationStyle) . "\n\n";

        $context .= "Resumen financiero actual:\n";

        $context .= "- Balance: {$balance}€\n";
        $context .= "- Ingresos totales: {$totalIncome}€\n";
        $context .= "- Gastos totales: {$totalExpense}€\n";
        $context .= "- Transacciones registradas: {$transactionsCount}\n";
        $context .= "- Objetivos de ahorro: {$savingGoalsCount}\n\n";

        if ($preference) {

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
- Refiérete al usuario siempre como \"{$preferredName}\".
- Adapta tu tono al estilo {$conversationStyle}.
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


        $result = $this->callGeminiApi($prompt);


        CoachMessage::create([
            'user_id' => $user->id,
            'message' => $validated['message'],
            'response' => $result['reply'],
            'mood' => $result['mood'],
        ]);


        return response()->json([
            'reply' => $result['reply'],
            'mood' => $result['mood'],
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

    public function demoChat(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'userName' => 'nullable|string|max:255',
            'userTone' => 'nullable|string|in:formal,cercano,motivador,amigable',
        ]);

        $preferredName = $validated['userName'] ?? 'amigo';
        $conversationStyle = $validated['userTone'] === 'amigable'
            ? 'cercano'
            : ($validated['userTone'] ?? 'cercano');

        $context = "Eres Billetín, el coach financiero de BilleterIA. Esta es una conversación de demostración con un usuario no registrado.\n\n";
        $context .= "Nombre del usuario: {$preferredName}\n";
        $context .= "Estilo de conversación: {$conversationStyle}\n";
        $context .= $this->styleInstructions($conversationStyle) . "\n\n";

        $context .= "Resumen financiero actual (DATOS DE EJEMPLO):\n";
        $context .= "- Balance: 1250€\n";
        $context .= "- Ingresos totales: 2500€\n";
        $context .= "- Gastos totales: 1250€\n";
        $context .= "- Objetivos de ahorro: 1\n\n";

        $context .= "Objetivos de ahorro:\n";
        $context .= "- Viaje a Japón\n  Objetivo: 3000€\n  Ahorrado: 1500€\n  Progreso: 50%\n\n";

        $context .= "Últimas transacciones:\n";
        $context .= "- Nómina: 1800€ (income)\n";
        $context .= "- Supermercado: 75€ (expense)\n";
        $context .= "- Netflix: 13€ (expense)\n\n";

        $context .= "
Instrucciones:
- Eres Billetín, el coach financiero de BilleterIA.
- Responde SIEMPRE en español.
- Responde primero a la pregunta concreta del usuario basándote en los datos de ejemplo.
- Recuerda que es un usuario en modo DEMO, por lo que puedes animarlo sutilmente a registrarse gratis para conectar sus propios datos reales.
- Sé cercano, útil y motivador.
- Máximo 120 palabras.
- Puedes usar emojis cuando aporten valor.

IMPORTANTE: Devuelve SIEMPRE un JSON válido.
Formato:
{
    \"reply\": \"respuesta para el usuario\",
    \"mood\": \"normal\"
}
Estados permitidos: normal, happy, thinking, worried
NO escribas texto fuera del JSON.
";

        $prompt =
            $context .
            "\n\nPregunta actual del usuario:\n" .
            $validated['message'];

        $result = $this->callGeminiApi($prompt);

        return response()->json([
            'reply' => $result['reply'],
            'mood' => $result['mood'],
        ]);
    }

    private function callGeminiApi(string $prompt): array
    {
        $apiKey = config('services.gemini.api_key');

        if (empty($apiKey)) {
            return [
                'reply' => 'Hola, soy Billetín. Para activar mis consejos con Inteligencia Artificial en producción, recuerda añadir la clave GEMINI_API_KEY en las variables de entorno de Render.',
                'mood' => 'thinking',
            ];
        }

        $models = ['gemini-2.5-flash', 'gemini-3.7-flash', 'gemini-2.5-flash-lite'];

        foreach ($models as $model) {
            try {
                $response = Http::timeout(15)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                    [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt]
                                ]
                            ]
                        ]
                    ]
                );

                if ($response->successful()) {
                    $content = $response->json('candidates.0.content.parts.0.text');

                    if (!empty($content)) {
                        $content = trim($content);
                        $content = preg_replace('/^```json|```$/m', '', $content);
                        $decoded = json_decode($content, true);

                        if (json_last_error() === JSON_ERROR_NONE && isset($decoded['reply'])) {
                            $reply = $decoded['reply'];
                            $mood = $decoded['mood'] ?? 'normal';
                        } else {
                            $reply = $content;
                            $mood = 'normal';
                        }

                        $reply = preg_replace('/\*+/', '', $reply);
                        $reply = str_replace('#', '', $reply);

                        return [
                            'reply' => $reply,
                            'mood' => in_array($mood, ['normal', 'happy', 'thinking', 'worried']) ? $mood : 'normal',
                        ];
                    }
                }
            } catch (\Exception $e) {

                continue;
}
        }

        return [
            'reply' => '¡Hola! En este momento Billetín está experimentando una breve pausa en el servicio de IA. Vuelve a intentarlo en unos instantes.',
            'mood' => 'worried',
        ];
    }
}