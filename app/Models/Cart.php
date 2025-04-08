<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';
    // Вказуємо, що ці атрибути можна заповнювати
    protected $fillable = [
        'user_id',
        'accommodation_id',
        'checkin_date',
        'checkout_date',
        'guests_count',
        'accommodation_photo',
        'created_at',
        'updated_at',
    ];


    // Визначення зв'язку з таблицею cart_meal
    public function mealOptions()
    {
        return $this->belongsToMany(MealOption::class, 'cart_meal', 'cart_id', 'meal_option_id')
            ->withPivot('guests_count');
    }

    // Відношення до Accommodation
    public function accommodations()
    {
        return $this->hasMany(CartAccommodation::class);
    }

    public function cartAccommodations()
    {
        return $this->hasMany(CartAccommodation::class, 'cart_id');
    }
    public function accommodationMealOptions()
    {
        return $this->belongsToMany(MealOption::class, 'cart_accommodation_meal_option', 'cart_accommodation_id', 'meal_option_id')
            ->withPivot('guests_count');
    }
}
