<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoachPreference extends Model
{
    protected $fillable = [
        'user_id',
        'conversation_style',
        'coach_background',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}