<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'saving_goal_id',
        'type',
        'title',
        'description',
        'amount',
        'saving_amount',
        'transaction_date',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function savingGoal()
    {
        return $this->belongsTo(SavingGoal::class);
    }
}
