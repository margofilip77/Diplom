<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = ['name', 'description'];

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function accommodations()
    {
        return $this->hasMany(Accommodation::class);
    }
    public function packages()
    {
        return $this->hasMany(Package::class);
    }
}