<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Cart;
use App\Models\Favorite;
use App\Models\Review; // Додаємо модель Review
use App\Models\Service;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'role',
        'is_blocked',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_blocked' => 'boolean',
        ];
    }

    // Зв’язок із кошиком
    public function cart()
    {
        return $this->hasOne(Cart::class); // Якщо кожен користувач має тільки один кошик
        // return $this->hasMany(Cart::class); // Якщо кожен користувач може мати кілька кошиків
    }

    // Зв’язок із улюбленими помешканнями
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    // Метод для перевірки, чи помешкання є улюбленим
    public function hasFavorited($accommodationId)
    {
        return $this->favorites()->where('accommodation_id', $accommodationId)->exists();
    }

    // Додаємо зв’язок із відгуками
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    // Метод для перевірки ролі
    public function isProvider()
    {
        return $this->role === 'provider';
    }
    public function services()
    {
        return $this->hasMany(Service::class);
    }
    public function accommodations()
    {
        return $this->hasMany(Accommodation::class);
    }

}