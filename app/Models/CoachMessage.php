<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoachMessage extends Model
{
    protected $fillable = [
        'user_id',
        'message',
        'response',
        'mood',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}