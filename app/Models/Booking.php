<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'accommodation_id',
        'user_id',
        'meal_option_id', // Додано meal_option_id
    ];

    // Зв'язок з MealOption
    public function mealOption()
    {
        return $this->belongsTo(MealOption::class, 'meal_option_id');
    }

}
