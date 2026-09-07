<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\User;

class AchievementController extends Controller
{
    public function index()
    {
        return Achievement::all();
    }

    public function userAchievements(User $user)
    {
        $this->checkAndGrantAchievements($user);
        return $user->achievements()->get();
    }

    private function checkAndGrantAchievements(User $user): void
    {
        $achievementsToGrant = [];

        // ID 1: Primer gasto
        if ($user->transactions()->where('type', 'expense')->exists()) {
            $achievementsToGrant[] = 1;
        }

        // ID 2: Primer ahorro
        if ($user->savingGoals()->exists()) {
            $achievementsToGrant[] = 2;
        }

        $totalSavings = (float) $user->savingGoals()->sum('current_amount');

        // ID 3: Ahorrador principiante (100€)
        if ($totalSavings >= 100) {
            $achievementsToGrant[] = 3;
        }

        // ID 5: Ahorrador experto (500€)
        if ($totalSavings >= 500) {
            $achievementsToGrant[] = 5;
        }

        // ID 6: Organizado (10 transacciones)
        if ($user->transactions()->count() >= 10) {
            $achievementsToGrant[] = 6;
        }

        // ID 7: Objetivo cumplido
        if ($user->savingGoals()->whereColumn('current_amount', '>=', 'target_amount')->exists()) {
            $achievementsToGrant[] = 7;
        }

        if (!empty($achievementsToGrant)) {
            $user->achievements()->syncWithoutDetaching($achievementsToGrant);
        }
    }
}