<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartMealOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'meal_option_id',
        'guests_count',
    ];

    // Визначаємо зв'язок з кошиком
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    // Визначаємо зв'язок з типом харчування
    public function mealOption()
    {
        return $this->belongsTo(MealOption::class);
    }
}
