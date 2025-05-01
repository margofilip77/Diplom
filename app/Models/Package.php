<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = ['name', 'description', 'region_id', 'discount', 'price'];

    /**
     * Зв’язок "багато до багатьох" із моделлю Service.
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'package_service', 'package_id', 'service_id');
    }

    /**
     * Розрахунок ціни пакета зі знижкою.
     *
     * @return float
     */
    public function calculatePrice()
    {
        $totalPrice = $this->services->sum('price');
        $discount = $this->discount ?? 0;
        $discountedPrice = $totalPrice * (1 - $discount / 100);
        return round($discountedPrice, 2);
    }

    public function originalPrice()
    {
        return round($this->services->sum('price'), 2);
    }
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}