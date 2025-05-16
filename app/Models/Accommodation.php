<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\MealOption;

class Accommodation extends Model
{
    use HasFactory;

    // Якщо у тебе таблиця називається не 'accommodations' (Laravel очікує множину)
    protected $table = 'accommodations';

    // Дозволяємо масове заповнення цих полів
    protected $fillable = [
        'name',
        'description',
        'location',
        'price_per_night',
        'capacity',
        'settlement',
        'region',
        'region_id',
        'city_id',
        'detailed_description',
        'rules',
        'guests',
        'cancellation_policy',
        'children',
        'beds',
        'age_restrictions',
        'pets_allowed',
        'payment_options',
        'parties_allowed',
        'checkin_time',
        'checkout_time',
        'latitude',
        'longitude',
        'is_available',
        'user_id',
        'rejection_reason',
        'status',
    ];

    // Зв’язок "багато-до-багатьох" з послугами через проміжну таблицю accommodation_services
    public function services()
    {
        return $this->belongsToMany(Service::class, 'accommodation_services')
            ->withPivot('price_per_night') // Додаємо поле ціни для конкретного помешкання
            ->withTimestamps();
    }
    public function photos()
    {
        return $this->hasMany(AccommodationPhoto::class, 'accommodation_id');
    }
    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'accommodation_amenities', 'accommodation_id', 'amenity_id');
    }
    protected $casts = [
        'meal_options' => 'array',
    ];
    // Accommodation.php
 public function mealOptions()
    {
        return $this->belongsToMany(MealOption::class, 'accommodation_meal_option', 'accommodation_id', 'meal_option_id')
                    ->withPivot('price')
                    ->withTimestamps();
    }
    public function carts()
    {
        return $this->belongsToMany(Cart::class, 'cart_accommodation') // Точна назва таблиці
            ->withPivot('checkin_date', 'checkout_date', 'guests_count', 'accommodation_photo') // Додаємо додаткові атрибути
            ->withTimestamps();
    }
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function favoritedBy()
    {
        return $this->hasMany(Favorite::class);
    }
    public function bookedDates()
    {
        return $this->hasMany(BookedDate::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Додайте метод для обчислення середнього рейтингу
    public function averageRating()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    // Додайте метод для підрахунку кількості відгуків
    public function reviewsCount()
    {
        return $this->reviews()->count();
    }
    // Аксесор для обчислення середнього рейтингу
    public function getAverageRatingAttribute(): float
    {
        return $this->reviews()->avg('rating') ?? 0; // Повертаємо 0, якщо відгуків немає
    }

    // Аксесор для підрахунку кількості відгуків
    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count();
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
    public function getBookedDatesAttribute()
    {
        return $this->bookings()->select('checkin_date as start_date', 'checkout_date as end_date')->get()->map(function ($booking) {
            return [
                'start_date' => \Carbon\Carbon::parse($booking->start_date),
                'end_date' => \Carbon\Carbon::parse($booking->end_date),
            ];
        });
    }
// Додайте це відношення
public function bookings()
{
    return $this->hasMany(Booking::class, 'accommodation_id');
}
}
