<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePackage extends Model
{
    protected $fillable = ['name', 'description', 'price', 'region_id', 'city_id'];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_package_items');
    }
}