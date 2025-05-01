<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
    ];

    // Зв’язок із зручностями
    public function amenities(): HasMany
    {
        return $this->hasMany(Amenity::class);
    }
}
