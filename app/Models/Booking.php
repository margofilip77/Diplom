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
        return $this->hasMany(BookingService::class);
    }
}