<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'accommodation_id',
        'checkin_date',
        'checkout_date',
        'guests_count',
        'total_price',
        'name',
        'email',
        'phone',
        'comments',
        'payment_intent_id',
        'token'
    ];
    // Додайте ці поля до $dates або $casts

    // АБО використовуйте $casts (якщо ви використовуєте новішу версію Laravel)
    protected $casts = [
        'checkin_date' => 'date',
        'checkout_date' => 'date',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function services()
    {
        return $this->hasMany(BookingService::class, 'booking_id');
    }
    public function packages()
    {
        return $this->hasMany(BookingPackage::class, 'booking_id');
    }
    public function mealOptions()
    {
        return $this->belongsToMany(MealOption::class, 'booking_meal_option')
            ->withPivot('price', 'guests_count');
    }
}
