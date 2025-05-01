<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartAccommodationMealOption extends Model
{
    protected $table = 'cart_accommodation_meal_option';

    protected $fillable = [
        'cart_accommodation_id',
        'meal_option_id',
        'guests_count',
        'price',
    ];

    public function cartAccommodation()
    {
        return $this->belongsTo(CartAccommodation::class);
    }

    public function mealOption()
    {
        return $this->belongsTo(MealOption::class, 'meal_option_id');
    }
}