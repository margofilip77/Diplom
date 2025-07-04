<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealOption extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price'];

    // Відношення "багато до багатьох" з помешканнями
    public function accommodations()
    {
        return $this->belongsToMany(Accommodation::class, 'accommodation_meal_option')
                    ->withPivot('price'); // Додаємо price до відношення
    }
    public function cartAccommodations()
    {
        return $this->hasMany(CartAccommodationMealOption::class);
    }
}
