<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'accommodation_id',
        'guests',
        'checkin_date',
        'checkout_date',
        'total_price',
        'photo_url'
    ];

    // Визначаємо зв'язок з таблицею cart_meal_options
    public function mealOptions()
    {
        return $this->hasMany(CartMealOption::class);
    }

    // Визначаємо зв'язок з користувачем
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Визначаємо зв'язок з помешканням
    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class);
    }
}
