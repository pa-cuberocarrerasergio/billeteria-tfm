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
        return $user->achievements;
    }
}