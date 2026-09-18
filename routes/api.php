<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\SavingGoalController;
use App\Http\Controllers\Api\CoachPreferenceController;
use App\Http\Controllers\Api\AchievementController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\CoachController;



### Categories ###
Route::get('/categories', [CategoryController::class, 'index']);

### Middleware ###
Route::middleware('auth:sanctum')->group(function () { 
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/user/avatar', [AuthController::class, 'uploadAvatar']);

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show']);
    Route::put('/transactions/{transaction}', [TransactionController::class, 'update']);
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy']);

    // Saving Goals
    Route::get('/saving-goals', [SavingGoalController::class, 'index']);
    Route::post('/saving-goals', [SavingGoalController::class, 'store']);
    Route::get('/saving-goals/{savingGoal}', [SavingGoalController::class, 'show']);
    Route::put('/saving-goals/{savingGoal}', [SavingGoalController::class, 'update']);
    Route::delete('/saving-goals/{savingGoal}', [SavingGoalController::class, 'destroy']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // IA
    Route::post('/coach/chat', [CoachController::class, 'chat']);

    // Historial IA
    Route::get('/coach/history', [CoachController::class, 'history']);
});

### Coach Preferences ###

Route::get('/coach-preferences/{user}', [CoachPreferenceController::class, 'show']);
Route::put('/coach-preferences/{user}', [CoachPreferenceController::class, 'update']);

### Achievements ###
Route::get('/achievements', [AchievementController::class, 'index']);
Route::get('/users/{user}/achievements', [AchievementController::class, 'userAchievements']);

### Auth ###
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/google-login', [AuthController::class, 'googleLogin']);
### Demo IA ###
Route::post('/demo/coach/chat', [CoachController::class, 'demoChat']);

### test ai ###
Route::get('/test-gemini-key', function () {
    $apiKey = config('services.gemini.api_key');
    if (empty($apiKey)) {
        return response()->json([
            'status' => 'error',
            'message' => 'GEMINI_API_KEY is empty in Render env'
        ]);
    }

    try {
        $response = Illuminate\Support\Facades\Http::timeout(15)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent?key={$apiKey}",
            [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => 'Hola']
                        ]
                    ]
                ]
            ]
        );

        return response()->json([
            'key_exists' => true,
            'http_status' => $response->status(),
            'successful' => $response->successful(),
            'response' => $response->json() ?? $response->body(),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'key_exists' => true,
            'exception' => $e->getMessage()
        ], 500);
    }
});