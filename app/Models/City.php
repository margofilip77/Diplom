<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['name', 'region_id', 'latitude', 'longitude'];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function accommodations()
    {
        return $this->hasMany(Accommodation::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
