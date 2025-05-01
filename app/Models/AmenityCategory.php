<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AmenityCategory extends Model {

    use HasFactory;
protected $table = 'amenity_categories';
    protected $fillable = ['category_name'];

    public function amenities(): HasMany
    {
        // Вказуємо правильну назву зовнішнього ключа
        return $this->hasMany(Amenity::class, 'category_id');
    }
}
