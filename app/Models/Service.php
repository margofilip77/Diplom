<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'description', 'price', 'status','region_id', 'settlement', 'range', 'category_id', 'image', 'is_available', 'latitude', 'longitude', 'user_id', 'rejection_reason',];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_service', 'service_id', 'package_id');
    }
}