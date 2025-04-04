<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealOption extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price'];

    // MealOption.php
public function accommodations()
{
    return $this->belongsToMany(Accommodation::class, 'accommodation_meal_option')
                ->withPivot('price');
}

}
