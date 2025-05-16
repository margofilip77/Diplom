<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportMessage extends Model
{
    protected $fillable = ['name', 'email', 'message', 'is_viewed', 'last_viewed_at', 'response', 'responded_at'];

    protected $casts = [
        'is_viewed' => 'boolean',
        'last_viewed_at' => 'datetime',
        'responded_at' => 'datetime',
        'created_at' => 'datetime',
    ];
}