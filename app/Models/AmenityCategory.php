<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AmenityCategory extends Model {
    use HasFactory;

    protected $fillable = ['category_name'];

    public function amenities()
    {
        // Зв'язок з аміниті
        return $this->hasMany(Amenity::class);
    }
}
