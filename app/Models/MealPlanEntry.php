<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealPlanEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'week_start',
        'day_of_week',
        'meal',
        'dish_id',
    ];

    protected $casts = [
        'week_start' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dish()
    {
        return $this->belongsTo(Dish::class);
    }
}
