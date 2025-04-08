<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartAccommodation extends Model
{
    protected $table = 'cart_accommodation';
    protected $fillable = ['cart_id', 'accommodation_id', 'checkin_date', 'checkout_date', 'guests_count', 'accommodation_photo', 'price'];

    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function mealOptions()
    {
        return $this->hasMany(CartAccommodationMealOption::class);
    }
}
