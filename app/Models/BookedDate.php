<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookedDate extends Model
{
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];
    protected $fillable = ['accommodation_id', 'start_date', 'end_date'];
}