<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = [
        'title',
        'description',
        'icon',
        'points',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}