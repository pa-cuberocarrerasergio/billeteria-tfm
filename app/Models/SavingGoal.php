<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavingGoal extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'target_amount',
        'current_amount',
        'target_date',
        'priority',
        'emoji',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}
