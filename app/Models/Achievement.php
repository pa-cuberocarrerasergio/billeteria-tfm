<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    public function users()
    {
        return $this->belongsToMany(User::class)
                    ->withPivot('unlocked_at')
                    ->withTimestamps();
    }
}
