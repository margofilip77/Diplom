<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amenity extends Model {
    use HasFactory;
      // Зв'язок з категорією
      public function category()
      {
          return $this->belongsTo(AmenityCategory::class, 'category_id');
      }
  
      // Зв'язок з accommodation_amenities
      public function accommodations()
      {
          return $this->belongsToMany(Accommodation::class, 'accommodation_amenities', 'amenity_id', 'accommodation_id');
      }
  
}

