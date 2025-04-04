<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccommodationMealOption extends Model
{
    use HasFactory;

    protected $fillable = ['accommodation_id', 'meal_option_id'];
}
