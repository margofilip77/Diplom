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
    protected $fillable = ['name', 'description', 'price_per_night', 'image'];

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
    return $this->belongsToMany(MealOption::class, 'accommodation_meal_option')
                ->withPivot('price'); // Тут можна отримати також ціну
}
public function carts()
{
    return $this->belongsToMany(Cart::class, 'cart_accommodation') // Точна назва таблиці
        ->withPivot('checkin_date', 'checkout_date', 'guests_count', 'accommodation_photo') // Додаємо додаткові атрибути
        ->withTimestamps();
}

}
