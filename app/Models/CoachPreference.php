<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoachPreference extends Model
{
    public function user()
{
    return $this->belongsTo(User::class);
}
}
