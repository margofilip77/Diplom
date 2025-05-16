<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['accommodation_id', 'user_id', 'comment', 'rating', 'is_blocked', 'blocked_at' ];
    protected $dates = ['blocked_at', 'created_at', 'updated_at'];
    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    
    }
    
}