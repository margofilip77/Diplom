<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Amenity extends Model {
    protected $table = 'amenities';
    protected $fillable = [
        'name',
        'category_id',
    ];

    // Зв’язок із категорією
    public function category(): BelongsTo
    {
        // Вказуємо правильну назву зовнішнього ключа
        return $this->belongsTo(AmenityCategory::class, 'category_id');
    }

    // Зв’язок із помешканнями
    public function accommodations(): BelongsToMany
    {
        return $this->belongsToMany(Accommodation::class, 'accommodation_amenities');
    }
  
}

