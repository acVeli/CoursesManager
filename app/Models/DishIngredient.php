<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DishIngredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'dish_id',
        'name',
        'quantity',
        'unit',
        'baby',
    ];

    public function dish()
    {
        return $this->belongsTo(Dish::class);
    }
}
